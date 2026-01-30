<?php

namespace App\Http\Controllers;

use App\Models\VirtualTryon;
use App\Models\Product;
use App\Services\FitroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VirtualTryonController extends Controller
{
    protected $fitroomService;

    public function __construct(FitroomService $fitroomService)
    {
        $this->fitroomService = $fitroomService;
    }

    /**
     * Show virtual try-on page
     */
    public function index(Request $request)
    {
        $product = null;
        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
        }
        return view('virtual-tryon.index', compact('product'));
    }

    /**
     * Upload images and create try-on task
     */
    public function upload(Request $request)
    {
        // Log incoming request for debugging
        Log::info('Virtual Try-On Upload Request', [
            'has_model_image' => $request->hasFile('model_image'),
            'has_garment_image' => $request->hasFile('garment_image'),
            'product_id' => $request->product_id,
            'clothing_type' => $request->clothing_type,
            'model_image_info' => $request->hasFile('model_image') ? [
                'name' => $request->file('model_image')->getClientOriginalName(),
                'size' => $request->file('model_image')->getSize(),
                'mime' => $request->file('model_image')->getMimeType(),
            ] : null,
        ]);

        $request->validate([
            'model_image' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'garment_image' => 'required_without:product_id|image|mimes:jpeg,jpg,png|max:5120',
            'product_id' => 'nullable|exists:products,id',
            'clothing_type' => 'required|in:upper,lower,full',
        ], [
            'model_image.required' => 'Vui lòng chọn ảnh của bạn',
            'model_image.image' => 'File phải là ảnh',
            'model_image.mimes' => 'Ảnh phải có định dạng: jpeg, jpg, png',
            'model_image.max' => 'Kích thước ảnh không được vượt quá 5MB',
            'garment_image.required_without' => 'Vui lòng chọn ảnh quần áo hoặc chọn sản phẩm',
            'garment_image.image' => 'File phải là ảnh',
            'garment_image.mimes' => 'Ảnh phải có định dạng: jpeg, jpg, png',
            'garment_image.max' => 'Kích thước ảnh không được vượt quá 5MB',
            'clothing_type.required' => 'Vui lòng chọn loại quần áo',
        ]);

        try {
            // Upload images to local storage
            $modelPath = $request->file('model_image')->store('virtual-tryon/models', 'public');
            $modelLocalPath = storage_path('app/public/' . $modelPath);
            
            $garmentPath = null;
            $garmentLocalPath = null;

            if ($request->hasFile('garment_image')) {
                $garmentPath = $request->file('garment_image')->store('virtual-tryon/garments', 'public');
                $garmentLocalPath = storage_path('app/public/' . $garmentPath);
            } elseif ($request->product_id) {
                // Use product image
                $product = Product::find($request->product_id);
                // We'll store the public relative path for DB reference, checking how it's used later
                // If the product image is just a filename, prepend the folder
                $garmentPath = 'uploads/products/' . $product->image; 
                // Full system path for Fitroom
                $garmentLocalPath = public_path($garmentPath);
            }

            Log::info('Creating Fitroom try-on task...', [
                'model_path' => $modelPath,
                'garment_path' => $garmentPath,
                'clothing_type' => $request->clothing_type,
            ]);

            // Create try-on task by uploading files directly to Fitroom
            $hdMode = config('services.fitroom.quality', 'standard') === 'hd';
            
            $task = $this->fitroomService->createTryOnTask(
                $modelLocalPath,
                $garmentLocalPath,
                $request->clothing_type,
                $hdMode
            );

            Log::info('Fitroom task created', $task);

            // Calculate credits (HD = 2, Standard = 1)
            $credits = $hdMode ? 2 : 1;

            // Save to database
            $tryon = VirtualTryon::create([
                'user_id' => Auth::id(),
                'user_image' => $modelPath,
                'garment_image' => $garmentPath,
                'fitroom_task_id' => $task['task_id'],
                'clothing_type' => $request->clothing_type,
                'quality' => $hdMode ? 'hd' : 'standard',
                'status' => 'processing',
                'credits_used' => $credits,
            ]);

            return response()->json([
                'success' => true,
                'tryon_id' => $tryon->id,
                'task_id' => $task['task_id'],
                'status' => $task['status'],
                'message' => 'Virtual try-on started successfully!',
            ]);

        } catch (\Exception $e) {
            Log::error('Virtual Try-On Upload Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create virtual try-on. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get try-on result (polling endpoint)
     */
    public function getResult($id)
    {
        // Increase execution time to prevent timeout during large image download
        set_time_limit(120);
        $tryon = VirtualTryon::findOrFail($id);

        // If already completed, return result immediately
        if ($tryon->isCompleted()) {
            return response()->json([
                'status' => 'completed',
                'result_image' => $tryon->result_image_url,
                'processing_time' => $tryon->processing_time,
            ]);
        }

        // If failed, return error
        if ($tryon->isFailed()) {
            return response()->json([
                'status' => 'failed',
                'message' => $tryon->error_message,
            ], 500);
        }

        try {
            // Poll Fitroom API for status
            $response = $this->fitroomService->getTaskStatus($tryon->fitroom_task_id);

            Log::info('Fitroom task status', $response);

            // Handle different statuses
            $status = $response['status'];

            if ($status === 'COMPLETED') {
                // Download result image from signed URL - save to public directory
                $resultPath = 'uploads/virtual-tryon/results/' . Str::uuid() . '.jpg';
                $fullPath = public_path($resultPath);

                // Create directory if not exists
                $dir = dirname($fullPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                if ($this->fitroomService->downloadResultImage($response['download_signed_url'], $fullPath)) {
                    $tryon->update([
                        'result_image' => $resultPath,
                        'status' => 'completed',
                        'processing_time' => isset($response['completed_at']) && isset($response['started_at']) 
                            ? $response['completed_at'] - $response['started_at'] 
                            : null,
                    ]);

                    return response()->json([
                        'status' => 'completed',
                        'result_image' => asset($resultPath),
                        'processing_time' => $tryon->processing_time,
                    ]);
                } else {
                    $tryon->update([
                        'status' => 'failed',
                        'error_message' => 'Failed to download result image',
                    ]);

                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Failed to download result image from provider',
                    ], 500);
                }
            } elseif ($status === 'FAILED') {
                $tryon->update([
                    'status' => 'failed',
                    'error_message' => $response['error'] ?? 'Unknown error',
                ]);

                return response()->json([
                    'status' => 'failed',
                    'message' => $response['error'] ?? 'Processing failed',
                ], 500);
            }

            // Still CREATED or PROCESSING
            return response()->json([
                'status' => 'processing',
                'progress' => $response['progress'] ?? 0,
                'message' => 'Your virtual try-on is being processed...',
            ]);

        } catch (\Exception $e) {
            Log::error('Get Result Error', [
                'message' => $e->getMessage(),
            ]);

            $tryon->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to get result: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show gallery of user's try-ons
     */
    public function gallery()
    {
        $tryons = VirtualTryon::where('user_id', Auth::id())
            ->completed()
            ->latest()
            ->paginate(12);

        return view('virtual-tryon.gallery', compact('tryons'));
    }
}
