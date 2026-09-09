<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Store Billing — New Order</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-header: #1e293b;
            --bg-card: #ffffff;
            --border-color: #cbd5e1;
            --primary-blue: #2563eb;
            --primary-blue-hover: #1d4ed8;
            --primary-green: #16a34a;
            --primary-green-hover: #15803d;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --alert-bg: #fffbe6;
            --alert-border: #f59e0b;
            --alert-text: #92400e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Header */
        .top-bar {
            background-color: var(--bg-header);
            color: #ffffff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .top-bar h1 {
            font-size: 1.25rem;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .top-bar .subtitle {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Container Layout */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2rem;
            width: 100%;
        }

        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
        }

        /* Cards & Section Titles */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        /* Form Inputs */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .form-group label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .form-control {
            padding: 0.6rem 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            font-family: inherit;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        /* Product Table */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .product-table th, .product-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .product-table th {
            background-color: #f1f5f9;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-blue {
            background-color: var(--primary-blue);
            color: #ffffff;
        }

        .btn-blue:hover {
            background-color: var(--primary-blue-hover);
        }

        .btn-green {
            background-color: var(--primary-green);
            color: #ffffff;
            width: 100%;
            padding: 0.85rem;
            font-size: 1.05rem;
        }

        .btn-green:hover {
            background-color: var(--primary-green-hover);
        }

        .btn-danger {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }

        .btn-danger:hover {
            background-color: #fee2e2;
        }

        /* Payment Summary Block */
        .payment-card {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .summary-row.grand-total {
            font-size: 1.15rem;
            font-weight: 700;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 2px dashed var(--border-color);
            color: var(--text-dark);
        }

        .balance-info {
            margin-top: 0.75rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .balance-amount {
            font-weight: 700;
            color: #047857;
            font-size: 1rem;
        }

        /* Low Stock Alert Sidebar */
        .alert-box {
            background-color: var(--alert-bg);
            border: 1px solid var(--alert-border);
            border-radius: 8px;
            padding: 1.25rem;
            color: var(--alert-text);
        }

        .alert-box h3 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .alert-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .alert-list li {
            font-size: 0.875rem;
            padding-bottom: 0.35rem;
            border-bottom: 1px dashed rgba(146, 64, 14, 0.2);
        }

        /* Toast Banners */
        .toast {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: none;
        }

        .toast-error {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .toast-success {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
        }

        /* Receipt Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: #ffffff;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            padding: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .receipt-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .modal-content, .modal-content * {
                visibility: visible;
            }
            .modal-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="top-bar">
        <h1>Store Billing — New Order</h1>
        <span class="subtitle">Store Order & Inventory Mini-System</span>
    </header>

    <!-- Main Container -->
    <div class="container">
        
        <!-- Left Section (Main Form) -->
        <main>
            <!-- Global Feedback Banners -->
            <div id="toastError" class="toast toast-error"></div>
            <div id="toastSuccess" class="toast toast-success"></div>

            <!-- Customer Details Card -->
            <div class="card">
                <h2 class="section-title">Customer</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="customerEmail">Email</label>
                        <input type="email" id="customerEmail" class="form-control" placeholder="e.g. thomas@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="customerName">Name</label>
                        <input type="text" id="customerName" class="form-control" placeholder="auto-filled if email exists" required>
                    </div>
                </div>
            </div>

            <!-- Products Selection Card -->
            <div class="card">
                <h2 class="section-title">Products</h2>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Product</th>
                            <th style="width: 15%;">Qty</th>
                            <th style="width: 18%;">Price</th>
                            <th style="width: 17%;">Line Total</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="productRows">
                        <!-- Dynamic Rows Injected via JS -->
                    </tbody>
                </table>

                <button type="button" id="btnAddProduct" class="btn btn-blue">
                    + Add Product
                </button>
            </div>

            <!-- Payment & Checkout Card -->
            <div class="card">
                <h2 class="section-title">Payment</h2>
                <div class="payment-card">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="displaySubtotal">₹0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span id="displayTax">₹0.00</span>
                    </div>
                    <div class="summary-row grand-total">
                        <span>Grand Total</span>
                        <span id="displayGrandTotal">₹0.00</span>
                    </div>

                    <div class="form-group" style="margin-top: 1rem;">
                        <label for="amountGiven">Amount Given by Customer</label>
                        <input type="number" id="amountGiven" class="form-control" placeholder="e.g. 250" step="0.01">
                    </div>

                    <div class="balance-info">
                        <span>Balance to Return:</span>
                        <span id="displayBalance" class="balance-amount">₹0.00</span>
                    </div>
                </div>

                <div style="margin-top: 1.25rem;">
                    <button type="button" id="btnGenerateBill" class="btn btn-green">
                        Generate Bill
                    </button>
                </div>
            </div>
        </main>

        <!-- Right Section (Sidebar Alert) -->
        <aside>
            <div class="alert-box">
                <h3>⚠️ Low Stock Alert</h3>
                <ul id="lowStockList" class="alert-list">
                    <li style="color: var(--text-muted);">Loading low stock items...</li>
                </ul>
            </div>
        </aside>
    </div>

    <!-- Generated Receipt Modal -->
    <div id="receiptModal" class="modal-overlay">
        <div class="modal-content">
            <div class="receipt-header">
                <h2>STORE RECEIPT</h2>
                <p style="font-size: 0.8rem; color: var(--text-muted);" id="receiptDate"></p>
                <p style="font-weight: 600; font-size: 0.9rem; margin-top: 0.2rem;" id="receiptOrderId"></p>
            </div>

            <div style="margin-bottom: 1rem;">
                <p style="font-size: 0.85rem; color: var(--text-muted);">Customer Information:</p>
                <p style="font-weight: 600;" id="receiptCustomerName"></p>
                <p style="font-size: 0.85rem;" id="receiptCustomerEmail"></p>
            </div>

            <table class="product-table" style="margin-bottom: 1rem;">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="receiptItems"></tbody>
            </table>

            <div style="border-top: 2px dashed var(--border-color); padding-top: 0.75rem; margin-bottom: 1rem;">
                <div class="receipt-row"><span>Subtotal:</span><span id="receiptSubtotal"></span></div>
                <div class="receipt-row"><span>Tax:</span><span id="receiptTax"></span></div>
                <div class="receipt-row" style="font-weight: 700; font-size: 1.05rem;"><span>Grand Total:</span><span id="receiptGrandTotal"></span></div>
                <div class="receipt-row"><span>Amount Paid:</span><span id="receiptAmountGiven"></span></div>
                <div class="receipt-row" style="color: #047857; font-weight: 600;"><span>Change Returned:</span><span id="receiptBalance"></span></div>
            </div>

            <div class="no-print" style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" onclick="window.print()" class="btn btn-blue">Print Invoice</button>
                <button type="button" onclick="closeReceiptModal()" class="btn btn-green" style="width: auto;">New Order</button>
            </div>
        </div>
    </div>

    <!-- Frontend Application Logic -->
    <script>
        let availableProducts = [];
        let items = [];

        document.addEventListener('DOMContentLoaded', () => {
            fetchProducts();
            fetchLowStockAlerts();

            document.getElementById('btnAddProduct').addEventListener('click', addProductRow);
            document.getElementById('btnGenerateBill').addEventListener('click', generateBill);
            document.getElementById('customerEmail').addEventListener('blur', lookupCustomer);
            document.getElementById('amountGiven').addEventListener('input', calculateTotals);
        });

        // Fetch product catalog for selection
        async function fetchProducts() {
            try {
                const res = await fetch('/api/products');
                const json = await res.json();
                availableProducts = json.data || [];
                if (items.length === 0) {
                    addProductRow(); // Initial row
                }
            } catch (err) {
                showToastError('Failed to load product catalog.');
            }
        }

        // Fetch low stock items for sidebar
        async function fetchLowStockAlerts() {
            try {
                const res = await fetch('/api/products/low-stock');
                const json = await res.json();
                const products = json.data?.products || [];
                
                const list = document.getElementById('lowStockList');
                if (products.length === 0) {
                    list.innerHTML = '<li style="color: #047857;">All products have sufficient stock.</li>';
                    return;
                }

                list.innerHTML = products.map(p => 
                    `<li>• <strong>${escapeHtml(p.name)}</strong> — ${p.stock} units left</li>`
                ).join('');
            } catch (err) {
                console.error(err);
            }
        }

        // Auto-lookup customer name by email
        async function lookupCustomer() {
            const email = document.getElementById('customerEmail').value.trim();
            if (!email) return;

            try {
                const res = await fetch(`/api/customers/lookup?email=${encodeURIComponent(email)}`);
                const json = await res.json();
                if (json.data && json.data.name) {
                    document.getElementById('customerName').value = json.data.name;
                    showToastSuccess('Existing customer details auto-filled.');
                }
            } catch (err) {
                console.error(err);
            }
        }

        // Add a new product row to the billing table
        function addProductRow() {
            const rowId = Date.now() + Math.random();
            items.push({ id: rowId, product_id: '', quantity: 1 });
            renderRows();
        }

        // Remove a product row
        function removeRow(rowId) {
            if (items.length <= 1) {
                showToastError('At least one product line is required.');
                return;
            }
            items = items.filter(item => item.id !== rowId);
            renderRows();
        }

        // Render product rows table
        function renderRows() {
            const tbody = document.getElementById('productRows');
            tbody.innerHTML = items.map((item, index) => {
                const selectedProd = availableProducts.find(p => p.id == item.product_id);
                const unitPrice = selectedProd ? parseFloat(selectedProd.price) : 0;
                const taxRate = selectedProd ? parseFloat(selectedProd.tax_percentage || 0) : 0;
                const lineSubtotal = unitPrice * (item.quantity || 0);
                const lineTax = (lineSubtotal * taxRate) / 100;
                const lineTotal = lineSubtotal + lineTax;

                const optionsHtml = availableProducts.map(p => `
                    <option value="${p.id}" ${p.id == item.product_id ? 'selected' : ''}>
                        ${escapeHtml(p.name)} (${p.code}) - ₹${parseFloat(p.price).toFixed(2)} [Stock: ${p.stock}]
                    </option>
                `).join('');

                return `
                    <tr>
                        <td>
                            <select class="form-control" onchange="updateRowProduct(${item.id}, this.value)">
                                <option value="">+ dropdown to add product row</option>
                                ${optionsHtml}
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" value="${item.quantity}" min="1" oninput="updateRowQuantity(${item.id}, this.value)">
                        </td>
                        <td style="font-weight: 500;">₹${unitPrice.toFixed(2)}</td>
                        <td style="font-weight: 600;">₹${lineTotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn-danger" onclick="removeRow(${item.id})">✕</button>
                        </td>
                    </tr>
                `;
            }).join('');

            calculateTotals();
        }

        function updateRowProduct(rowId, productId) {
            const item = items.find(i => i.id === rowId);
            if (item) {
                item.product_id = productId;
                renderRows();
            }
        }

        function updateRowQuantity(rowId, qty) {
            const item = items.find(i => i.id === rowId);
            if (item) {
                item.quantity = parseInt(qty) || 1;
                renderRows();
            }
        }

        // Calculate Subtotal, Tax, Grand Total, and Balance Return
        function calculateTotals() {
            let subtotal = 0;
            let tax = 0;

            items.forEach(item => {
                const prod = availableProducts.find(p => p.id == item.product_id);
                if (prod) {
                    const price = parseFloat(prod.price);
                    const taxRate = parseFloat(prod.tax_percentage || 0);
                    const lineSub = price * (item.quantity || 0);
                    const lineTax = (lineSub * taxRate) / 100;

                    subtotal += lineSub;
                    tax += lineTax;
                }
            });

            const grandTotal = subtotal + tax;
            const amountGiven = parseFloat(document.getElementById('amountGiven').value) || 0;
            const balance = Math.max(0, amountGiven - grandTotal);

            document.getElementById('displaySubtotal').innerText = `₹${subtotal.toFixed(2)}`;
            document.getElementById('displayTax').innerText = `₹${tax.toFixed(2)}`;
            document.getElementById('displayGrandTotal').innerText = `₹${grandTotal.toFixed(2)}`;
            document.getElementById('displayBalance').innerText = `₹${balance.toFixed(2)}`;
        }

        // Handle Bill Generation & Order Submission
        async function generateBill() {
            hideToasts();

            const email = document.getElementById('customerEmail').value.trim();
            const name = document.getElementById('customerName').value.trim();
            const amountGiven = parseFloat(document.getElementById('amountGiven').value) || 0;

            if (!email || !name) {
                showToastError('Please enter customer email and name.');
                return;
            }

            const validItems = items
                .filter(i => i.product_id && i.quantity > 0)
                .map(i => ({ product_id: parseInt(i.product_id), quantity: parseInt(i.quantity) }));

            if (validItems.length === 0) {
                showToastError('Please select at least one valid product.');
                return;
            }

            const payload = {
                customer: { name, email },
                items: validItems
            };

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if (!res.ok) {
                    const errorMsg = json.message || (json.errors ? Object.values(json.errors).flat().join(', ') : 'Order creation failed.');
                    showToastError(errorMsg);
                    return;
                }

                showToastSuccess('Order processed successfully!');
                displayReceipt(json.data, amountGiven);
                
                // Refresh catalog & low stock list
                fetchProducts();
                fetchLowStockAlerts();

            } catch (err) {
                showToastError('Network or server error occurred while creating order.');
            }
        }

        // Display generated bill modal
        function displayReceipt(order, amountGiven) {
            document.getElementById('receiptOrderId').innerText = `Order #${order.id}`;
            document.getElementById('receiptDate').innerText = new Date().toLocaleString();
            document.getElementById('receiptCustomerName').innerText = order.customer.name;
            document.getElementById('receiptCustomerEmail').innerText = order.customer.email;

            const itemsTbody = document.getElementById('receiptItems');
            itemsTbody.innerHTML = order.order_items.map(item => `
                <tr>
                    <td>${escapeHtml(item.product.name)}</td>
                    <td>${item.quantity}</td>
                    <td>₹${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td>₹${parseFloat(item.line_total).toFixed(2)}</td>
                </tr>
            `).join('');

            const grandTotal = parseFloat(order.grand_total);
            const balance = Math.max(0, amountGiven - grandTotal);

            document.getElementById('receiptSubtotal').innerText = `₹${parseFloat(order.subtotal).toFixed(2)}`;
            document.getElementById('receiptTax').innerText = `₹${parseFloat(order.tax).toFixed(2)}`;
            document.getElementById('receiptGrandTotal').innerText = `₹${grandTotal.toFixed(2)}`;
            document.getElementById('receiptAmountGiven').innerText = `₹${amountGiven.toFixed(2)}`;
            document.getElementById('receiptBalance').innerText = `₹${balance.toFixed(2)}`;

            document.getElementById('receiptModal').style.display = 'flex';
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').style.display = 'none';
            // Reset form
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerName').value = '';
            document.getElementById('amountGiven').value = '';
            items = [];
            addProductRow();
        }

        function showToastError(msg) {
            const el = document.getElementById('toastError');
            el.innerText = msg;
            el.style.display = 'block';
        }

        function showToastSuccess(msg) {
            const el = document.getElementById('toastSuccess');
            el.innerText = msg;
            el.style.display = 'block';
        }

        function hideToasts() {
            document.getElementById('toastError').style.display = 'none';
            document.getElementById('toastSuccess').style.display = 'none';
        }

        function escapeHtml(text) {
            return String(text).replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            })[m]);
        }
    </script>
</body>
</html>
