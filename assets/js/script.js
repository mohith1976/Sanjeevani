$(document).ready(function() {
    const loginCode = "<?php echo $_SESSION['user_login_code']; ?>";

    // Initialize DataTables for Suppliers
    const supplierTable = $('#supplierTable').DataTable({
        ajax: {
            url: '../public/get_suppliers.php',
            method: 'POST',
            data: { login_code: loginCode },
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'supplierName' },
            { data: 'contact_info' },
            {
                data: 'id',
                render: function(data) {
                    return '<button class="btn btn-danger delete-supplier" data-id="' + data + '">Delete</button>';
                }
            }
        ]
    });

    // Initialize DataTables for Categories
const categoryTable = $('#categoryTable').DataTable({
    ajax: {
        url: '../public/get_categories.php',
        method: 'POST',
        data: { login_code: loginCode },
        dataSrc: 'data'
    },
    columns: [
        { data: 'id' },
        { data: 'categoryName' },
        {
            data: 'id',
            render: function(data) {
                return '<button class="btn btn-danger delete-category" data-id="' + data + '">Delete</button>';
            }
        }
    ]
});


    // Initialize DataTables for Medicines
    const medicineTable = $('#medicineTable').DataTable({
        ajax: {
            url: '../public/get_medicines.php',
            method: 'POST',
            data: { login_code: loginCode },
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'medicineName' },
            { data: 'categoryName' },
            { data: 'supplierName' },
            { data: 'quantity' },
            { data: 'expiry_date' },
            {
                data: 'id',
                render: function(data) {
                    return '<button class="btn btn-danger delete-medicine" data-id="' + data + '">Delete</button>';
                }
            }
        ]
    });

    // Add Supplier
$('#addSupplierForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../public/add_supplier.php',
        method: 'POST',
        data: $(this).serialize() + '&login_code=' + loginCode,
        success: function() {
            // Reload the supplier table without showing an alert
            supplierTable.ajax.reload();
            // Reset the form after submission
            $('#addSupplierForm')[0].reset();
        }
    });
});


    // Delete Supplier
    $('#supplierTable').on('click', '.delete-supplier', function() {
        const supplierId = $(this).data('id');
        $.ajax({
            url: '../public/delete_supplier.php',
            method: 'POST',
            data: { id: supplierId, login_code: loginCode },
            success: function(response) {
                alert(response); // Optional: Show response message
                supplierTable.ajax.reload();
            }
        });
    });

    // Add Category
$('#addCategoryForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '../public/add_category.php',
        method: 'POST',
        data: $(this).serialize() + '&login_code=' + loginCode,
        success: function() {
            // Reload the category table without showing an alert
            categoryTable.ajax.reload();
            // Reset the form after submission
            $('#addCategoryForm')[0].reset();
        }
    });
});


    // Delete Category
$('#categoryTable').on('click', '.delete-category', function() {
    const categoryId = $(this).data('id');
    $.ajax({
        url: '../public/delete_category.php',
        method: 'POST',
        data: { id: categoryId, login_code: loginCode },
        success: function(response) {
            const result = JSON.parse(response); // Parse the JSON response
            if (result.status === 'success') {
                categoryTable.ajax.reload(null, false); // Reload DataTable without resetting pagination
            } else {
                console.error(result.message); // Log any error to the console
            }
        }
    });
});



    // Add Medicine
    $('#addMedicineForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '../public/add_medicine.php',
            method: 'POST',
            data: $(this).serialize() + '&login_code=' + loginCode,
            success: function(response) {
                alert(response); // Optional: Show response message
                medicineTable.ajax.reload();
                $('#addMedicineForm')[0].reset();
            }
        });
    });

    // Delete Medicine
    $('#medicineTable').on('click', '.delete-medicine', function() {
        const medicineId = $(this).data('id');
        $.ajax({
            url: '../public/delete_medicine.php',
            method: 'POST',
            data: { id: medicineId, login_code: loginCode },
            success: function(response) {
                alert(response); // Optional: Show response message
                medicineTable.ajax.reload();
            }
        });
    });

   
    
    // Populate categories and suppliers in the medicine form
$.ajax({
    url: '../public/get_categories.php',
    method: 'POST',
    data: { login_code: loginCode },
    success: function(response) {
        console.log(response); // Log the response
        const categories = JSON.parse(response).data;
        $('#medicine_category').empty();
        categories.forEach(category => {
            $('#medicine_category').append(`<option value="${category.id}">${category.categoryName}</option>`);
        });
    }
});

$.ajax({
    url: '../public/get_suppliers.php',
    method: 'POST',
    data: { login_code: loginCode },
    success: function(response) {
        console.log(response); // Log the response
        const suppliers = JSON.parse(response).data;
        $('#medicine_supplier').empty();
        suppliers.forEach(supplier => {
            $('#medicine_supplier').append(`<option value="${supplier.id}">${supplier.supplierName}</option>`);
        });
    }
});





});


    

