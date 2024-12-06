document.getElementById('product_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('imagePreviewImg');
    const previewText = document.getElementById('imagePreviewText');

    if (file) {
        const reader = new FileReader();
        previewText.style.display = "none";
        previewImg.style.display = "block";

        reader.addEventListener("load", function() {
            previewImg.setAttribute("src", this.result);
        });

        reader.readAsDataURL(file);
    } else {
        previewText.style.display = null;
        previewImg.style.display = null;
    }
});

function searchOrders() {
    let input = document.getElementById('orderSearch').value.toLowerCase();
    let table = document.getElementById('ordersTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td')[1];
        if (td) {
            let textValue = td.textContent || td.innerText;
            tr[i].style.display = textValue.toLowerCase().indexOf(input) > -1 ? '' : 'none';
        }
    }
}

function filterOrders() {
    let statusFilter = document.getElementById('statusFilter').value;
    let dateFilter = document.getElementById('dateFilter').value;
    let table = document.getElementById('ordersTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let statusTd = tr[i].getElementsByTagName('td')[4];
        let dateTd = tr[i].getElementsByTagName('td')[2];
        let statusValue = statusTd.textContent || statusTd.innerText;
        let dateValue = dateTd.textContent || dateTd.innerText;

        let display = true;

        if (statusFilter && statusValue !== statusFilter) {
            display = false;
        }
        if (dateFilter && dateValue !== dateFilter) {
            display = false;
        }

        tr[i].style.display = display ? '' : 'none';
    }
}

function updateOrderStatus(orderId, status) {
    alert('Order ' + orderId + ' status updated to ' + status);
}

function viewOrderDetails(orderId) {
    document.getElementById('orderDetails').innerHTML = 'Order Details for ' + orderId;
    document.getElementById('orderDetailsModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

let currentPage = 1;
let rowsPerPage = 10;

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderPage();
    }
}

function nextPage() {
    if ((currentPage * rowsPerPage) < document.getElementById('ordersTable').rows.length - 1) {
        currentPage++;
        renderPage();
    }
}

function renderPage() {
    let table = document.getElementById('ordersTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        if (i >= currentPage * rowsPerPage || i < (currentPage - 1) * rowsPerPage) {
            tr[i].style.display = 'none';
        } else {
            tr[i].style.display = '';
        }
    }
}

function exportOrders(format) {
    alert('Orders will be exported as ' + format);
}

function searchSuppliers() {
    let input = document.getElementById('supplierSearch').value.toLowerCase();
    let table = document.getElementById('suppliersTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let td = tr[i].getElementsByTagName('td')[1];
        if (td) {
            let textValue = td.textContent || td.innerText;
            tr[i].style.display = textValue.toLowerCase().indexOf(input) > -1 ? '' : 'none';
        }
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}


function addSupplier(event) {
    event.preventDefault(); 
    const name = document.getElementById('name').value;
    const contact = document.getElementById('contact').value;
    const email = document.getElementById('email').value;

    
    alert(`Supplier added: ${name}`);
    closeModal('addSupplierModal'); 
}


function viewSupplierDetails(supplierId) {
    document.getElementById('supplierDetails').innerHTML = `Details for Supplier ID: ${supplierId}`;
    document.getElementById('supplierDetailsModal').style.display = 'block';
}


function editSupplier(supplierId) {
    alert(`Edit Supplier ID: ${supplierId}`);
}

function deleteSupplier(supplierId) {
    if (confirm('Are you sure you want to delete this supplier?')) {
        alert(`Supplier ID ${supplierId} deleted.`);
    }
}

function searchNotifications() {
    let input = document.getElementById('notificationSearch').value.toLowerCase();
    let table = document.getElementById('notificationsTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let tdMessage = tr[i].getElementsByTagName('td')[2]; 
        if (tdMessage) {
            let textValue = tdMessage.textContent || tdMessage.innerText;
            tr[i].style.display = textValue.toLowerCase().indexOf(input) > -1 ? '' : 'none';
        }
    }
}

function filterNotifications() {
    let filter = document.getElementById('statusFilter').value;
    let table = document.getElementById('notificationsTable');
    let tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let tdStatus = tr[i].getElementsByTagName('td')[4]; 
        if (tdStatus) {
            let textValue = tdStatus.textContent || tdStatus.innerText;
            tr[i].style.display = filter === '' || textValue.toLowerCase() === filter ? '' : 'none';
        }
    }
}

function markAsRead(notificationId) {
    alert(`Notification ID ${notificationId} marked as read.`);
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        alert(`Notification ID ${notificationId} deleted.`);
    }
}
function filterReports() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    alert(`Filtering reports from ${startDate} to ${endDate}`);
}

function exportReports() {
    alert('Exporting reports as CSV...');
}

document.addEventListener('DOMContentLoaded', () => {
    const productSearch = document.getElementById('product-search');
    const productList = document.getElementById('product-list');
    const priceField = document.getElementById('price');
    const stockField = document.getElementById('stock');
    const quantityField = document.getElementById('quantity');
    const totalField = document.getElementById('total');
    const stockWarning = document.getElementById('stock-warning');

    productSearch.addEventListener('input', function () {
        let query = productSearch.value;
        if (query.length > 2) {
            fetch('fetch_products.php?q=' + query)
                .then(response => response.json())
                .then(data => {
                    productList.innerHTML = '';
                    data.forEach(product => {
                        let div = document.createElement('div');
                        div.textContent = product.name;
                        div.setAttribute('data-id', product.id);
                        div.setAttribute('data-price', product.price);
                        div.setAttribute('data-stock', product.stock);
                        productList.appendChild(div);
                    });
                    productList.style.display = 'block';
                });
        } else {
            productList.style.display = 'none';
        }
    });

    productList.addEventListener('click', function (e) {
        if (e.target && e.target.nodeName === "DIV") {
            let productName = e.target.textContent;
            let productPrice = e.target.getAttribute('data-price');
            let productStock = e.target.getAttribute('data-stock');

            productSearch.value = productName;
            priceField.value = productPrice;
            stockField.value = productStock;

            productList.style.display = 'none';
        }
    });

    quantityField.addEventListener('input', function () {
        let quantity = parseInt(quantityField.value);
        let stock = parseInt(stockField.value);
        let price = parseFloat(priceField.value);

        if (quantity > stock) {
            stockWarning.style.display = 'block';
            stockWarning.textContent = 'Insufficient stock!';
            quantityField.setCustomValidity('Not enough stock');
        } else {
            stockWarning.style.display = 'none';
            quantityField.setCustomValidity('');
        }

        totalField.value = (price * quantity).toFixed(2);
    });
});

document.getElementById("preview-button").addEventListener("click", function() {
    const name = document.getElementById("name").value;
    const price = document.getElementById("price").value;
    const description = document.getElementById("description").value;
    const stock = document.getElementById("stock").value;

    alert(`Preview:\n\nName: ${name}\nPrice: ${price}\nDescription: ${description}\nStock: ${stock}`);
});

document.getElementById("stock").addEventListener("input", function() {
    const stock = this.value;
    const alertMessage = document.getElementById("stock-alert");

    if (stock < 10) { 
        alertMessage.innerText = "Warning: Stock is low!";
    } else {
        alertMessage.innerText = "";
    }
});


function openModal(modalId) {
    $('#' + modalId).modal('show');
}


function closeModal(modalId) {
    $('#' + modalId).modal('hide');
}


function addSupplier(event) {
    event.preventDefault(); 

    const name = document.getElementById('name').value;
    const contact = document.getElementById('contact').value;
    const email = document.getElementById('email').value;

    const formData = new FormData();
    formData.append('name', name);
    formData.append('contact', contact);
    formData.append('email', email);

    
    fetch('add_supplier.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tableBody = document.getElementById('supplierTableBody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td>${data.supplier.id}</td>
                <td>${data.supplier.name}</td>
                <td>${data.supplier.contact}</td>
                <td>${data.supplier.email}</td>
                <td>${data.supplier.created_at}</td>
            `;

            tableBody.appendChild(newRow);
            closeModal('addSupplierModal'); 

            document.getElementById('successMessage').style.display = 'block';

            document.getElementById('addSupplierForm').reset();

            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'none';
            }, 3000);
        } else {
            alert('Failed to add supplier');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

