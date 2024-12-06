function restockProduct(productId) {
    fetch('restock_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product restocked successfully!');
            location.reload();
        } else {
            alert('Failed to restock product.');
        }
    })
    .catch(error => console.error('Error:', error));
}
