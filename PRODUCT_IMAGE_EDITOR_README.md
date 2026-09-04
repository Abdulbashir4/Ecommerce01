# Product Image Editor

Admin > Products > Image Editor adds a browser-based product graphics editor.

Features:
- Product selection and search
- Main/featured image editing
- Output resize up to 5000 x 5000
- Independent top/right/bottom/left padding
- Crop percentages on all four edges
- Rotate left/right and horizontal/vertical flip
- Center/fitting inside the output canvas
- Brightness, contrast and saturation adjustments
- Transparent or solid background
- Edge-connected background removal with adjustable tolerance
- Auto Design preset: square canvas, transparent background, background removal and balanced padding
- Saves the rendered WebP image to `public/uploads/products`
- Updates both `thumbnail` and `featured_image`
- Deletes the previous main image only after the new image is saved, and only when it is not still referenced by a product gallery or another product

The background remover is local browser-side processing, not an external AI API. It is designed for product photos with a reasonably uniform background; complex backgrounds should be reviewed before saving.
