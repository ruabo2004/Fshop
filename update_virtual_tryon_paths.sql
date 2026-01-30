-- Update existing virtual_tryons records to use new path format
UPDATE virtual_tryons 
SET result_image = REPLACE(result_image, 'virtual-tryon/results/', 'uploads/virtual-tryon/results/')
WHERE result_image LIKE 'virtual-tryon/results/%';
