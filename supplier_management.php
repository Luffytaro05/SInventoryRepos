<?php
include 'includes/header.php';
include 'db.php'; 

$stmt = $pdo->query("SELECT * FROM suppliers");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="assets/css/supplier_management.css">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Supplier Management</h1>

    <div id="successMessage" class="alert alert-success" style="display: none;">
        Supplier added successfully!
    </div>

    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search suppliers..." onkeyup="searchSuppliers()">
    </div>

    <button class="btn btn-primary mb-4" onclick="openModal('addSupplierModal')">Add Supplier</button>

    <div class="card">
        <div class="card-header">
            <h3>Suppliers List</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="supplierTable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="supplierTableBody">
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= htmlspecialchars($supplier['id']) ?></td>
                        <td><?= htmlspecialchars($supplier['supplier_name']) ?></td>
                        <td><?= htmlspecialchars($supplier['contact']) ?></td>
                        <td><?= htmlspecialchars($supplier['email']) ?></td>
                        <td><?= htmlspecialchars($supplier['address']) ?></td>
                        <td><?= htmlspecialchars($supplier['created_at']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="showMap('<?= htmlspecialchars($supplier['address']) ?>')">View Map</button>
                            <button class="btn btn-warning btn-sm" 
                                onclick="openEditModal(<?= $supplier['id'] ?>, 
                                '<?= htmlspecialchars($supplier['supplier_name']) ?>', 
                                '<?= htmlspecialchars($supplier['contact']) ?>', 
                                '<?= htmlspecialchars($supplier['email']) ?>',
                                '<?= htmlspecialchars($supplier['address']) ?>')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSupplier(<?= $supplier['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addSupplierModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Supplier</h5>
                <button type="button" class="close" onclick="closeModal('addSupplierModal')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" onsubmit="addSupplier(event)">
                    <div class="form-group">
                        <label for="name">Supplier Name:</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" class="form-control" id="address" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="editSupplierModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Supplier</h5>
                <button type="button" class="close" onclick="closeModal('editSupplierModal')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSupplierForm" onsubmit="editSupplier(event)">
                    <div class="form-group">
                        <label for="editName">Supplier Name:</label>
                        <input type="text" class="form-control" id="editName" required>
                    </div>
                    <div class="form-group">
                        <label for="editContact">Contact:</label>
                        <input type="text" class="form-control" id="editContact" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email:</label>
                        <input type="email" class="form-control" id="editEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address:</label>
                        <input type="text" class="form-control" id="editAddress" required>
                    </div>
                    <input type="hidden" id="editSupplierId">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="mapModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supplier Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="map"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    let map;
    let marker;

    function initializeMap() {
        map = L.map('map').setView([0, 0], 2); 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    function showMap(address) {
    $('#mapModal').modal('show');
    const apiUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`;

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                map.setView([lat, lng], 15);
                
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup(`<b>Supplier Location</b><br>${address}`)
                    .openPopup();
            } else {
                alert('Could not find location for the given address.');
            }
        })
        .catch(error => {
            console.error('Error fetching coordinates:', error);
            alert('Error fetching location data. Please try again later.');
        });
}
    function openModal(modalId) {
        $(`#${modalId}`).modal('show');
    }

    function closeModal(modalId) {
        $(`#${modalId}`).modal('hide');
    }
function addSupplier(event) {
    event.preventDefault();
    const supplier_name = $('#name').val();
    const contact = $('#contact').val();
    const email = $('#email').val();
    const address = $('#address').val();

    $.ajax({
        url: 'add_supplier.php',
        method: 'POST',
        data: { supplier_name, contact, email, address },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                alert('Supplier added successfully!');
                location.reload(); 
            } else {
                alert('Failed to add supplier: ' + result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', status, error);
            alert('An error occurred while adding the supplier. Please check your connection or try again later.');
        }
    });
}

function editSupplier(event) {
    event.preventDefault();
    const id = $('#editSupplierId').val();
    const supplier_name = $('#editName').val();
    const contact = $('#editContact').val();
    const email = $('#editEmail').val();
    const address = $('#editAddress').val();

    $.ajax({
        url: 'edit_supplier.php',
        method: 'POST',
        data: { id, supplier_name, contact, email, address },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                alert('Supplier updated successfully!');
                location.reload(); 
            } else {
                alert('Failed to update supplier: ' + result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', status, error);
            alert('An error occurred while updating the supplier. Please check your connection or try again later.');
        }
    });
}

function openEditModal(id, supplier_name, contact, email, address) {
    $('#editSupplierId').val(id);
    $('#editName').val(supplier_name);
    $('#editContact').val(contact);
    $('#editEmail').val(email);
    $('#editAddress').val(address);

    $('#editSupplierModal').modal('show');
}
function deleteSupplier(id) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        $.ajax({
            url: 'delete_supplier.php',
            method: 'POST',
            data: { id },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    alert('Supplier deleted successfully!');
                    location.reload(); 
                } else {
                    alert('Failed to delete supplier: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
                alert('An error occurred while deleting the supplier. Please check your connection or try again later.');
            }
        });
    }
}

    $(document).ready(function() {
        initializeMap();
    });
function searchSuppliers() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#supplierTableBody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let match = false;
        
        for (let i = 1; i < cells.length - 1; i++) { 
            if (cells[i].textContent.toLowerCase().includes(input)) {
                match = true;
                break;
            }
        }
        if (match) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

</script>
<?php include 'includes/footer.php' ?>
</body>
</html>



