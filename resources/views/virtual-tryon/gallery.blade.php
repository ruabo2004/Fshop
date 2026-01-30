@extends('layouts.app')

@section('content')
<main class="pt-5">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold">My Virtual Try-On Gallery</h1>
            <p class="lead text-muted">View all your virtual try-on results</p>
            <a href="{{ route('virtual-tryon.index') }}" class="btn btn-primary">
                Create New Try-On
            </a>
        </div>

        @if($tryons->count() > 0)
        <div class="row g-4">
            @foreach($tryons as $tryon)
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <img src="{{ $tryon->result_image_url }}" class="card-img-top" alt="Virtual Try-On Result">
                    <div class="card-body">
                        <p class="card-text small text-muted">
                            <i class="fa fa-clock"></i> {{ $tryon->created_at->diffForHumans() }}
                        </p>
                        <p class="card-text small">
                            <span class="badge bg-info">{{ ucfirst($tryon->clothing_type) }}</span>
                            <span class="badge bg-success">{{ ucfirst($tryon->quality) }}</span>
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ $tryon->result_image_url }}" class="btn btn-sm btn-primary" download>
                                <i class="fa fa-download"></i> Download
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewDetails({{ $tryon->id }})">
                                <i class="fa fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tryons->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <svg width="100" height="100" fill="currentColor" class="text-muted mb-3">
                <use href="#icon_hanger" />
            </svg>
            <h4>No Try-Ons Yet</h4>
            <p class="text-muted">Create your first virtual try-on to see it here!</p>
            <a href="{{ route('virtual-tryon.index') }}" class="btn btn-primary">
                Get Started
            </a>
        </div>
        @endif
    </div>
</main>
@endsection
