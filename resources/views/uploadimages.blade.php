<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Multiple Image Upload</title>
</head>

<body>
    <div>
        <h3>Upload Images</h3>
        <hr>
        <form method="POST" action="{{ route('units.uploadunit') }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div>
                <label>Category</label>
                <input type="text" name="unit_category_id" placeholder="Enter Unit Category">
                <label>Name</label>
                <input type="text" name="name" placeholder="Enter Unit Name">
                <label>Price</label>
                <input type="number" name="price" placeholder="Enter Unit Price">
            </div>
            <div>
                <label>Choose Images</label>
                <input type="file" name="images[]" multiple> <!-- Fixed input for multiple files -->
            </div>
            <hr>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>

</html>
