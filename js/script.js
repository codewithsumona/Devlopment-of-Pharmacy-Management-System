/* ==========================================================================
   Pharmacy Management System Prototype - Interactive Client Script
   SAD Lab Presentation Interactive Handlers
   ========================================================================== */

// Global POS Cart State
let posCart = [];

// Toast Notification Launcher
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = 'fa-circle-check';
    if (type === 'danger') icon = 'fa-circle-exclamation';
    if (type === 'info') icon = 'fa-circle-info';

    toast.innerHTML = `
        <i class="fa-solid ${icon}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// Universal Modal Handlers
function openModal(title, bodyHtml, footerHtml = '') {
    const overlay = document.getElementById('appModalOverlay');
    const titleElem = document.getElementById('modalTitle');
    const bodyElem = document.getElementById('modalBody');
    const footerElem = document.getElementById('modalFooter');

    if (!overlay) return;

    titleElem.innerText = title;
    bodyElem.innerHTML = bodyHtml;
    if (footerHtml) {
        footerElem.innerHTML = footerHtml;
        footerElem.style.display = 'flex';
    } else {
        footerElem.style.display = 'none';
    }

    overlay.classList.add('active');
}

function closeModal() {
    const overlay = document.getElementById('appModalOverlay');
    if (overlay) overlay.classList.remove('active');
}

// Global Filter for Data Tables
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;

    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        let textMatch = false;
        const cells = rows[i].getElementsByTagName('td');
        for (let j = 0; j < cells.length; j++) {
            if (cells[j]) {
                const text = cells[j].textContent || cells[j].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    textMatch = true;
                    break;
                }
            }
        }
        rows[i].style.display = textMatch ? '' : 'none';
    }
}

// Global Navbar Search Handler
function handleGlobalSearch(e) {
    if (e.key === 'Enter') {
        const query = e.target.value.trim();
        if (query.length > 0) {
            window.location.href = `../search/medicine_search.php?q=${encodeURIComponent(query)}`;
        }
    }
}

// Confirm Delete Modal Trigger
function confirmDelete(itemTitle, deleteUrl) {
    openModal(
        'Confirm Deletion',
        `<p style="font-size: 1rem; color: #475569;">Are you sure you want to delete <strong>${itemTitle}</strong>?</p>
         <p style="font-size: 0.85rem; color: #ef4444; margin-top: 0.5rem;"><i class="fa-solid fa-triangle-exclamation"></i> This action is for prototype presentation and can be reverted.</p>`,
        `<button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
         <a href="${deleteUrl}" class="btn btn-danger" onclick="showToast('Item deleted successfully', 'danger'); closeModal(); return true;">Delete Permanently</a>`
    );
}

// Notifications Popup Simulation
function toggleNotifications() {
    openModal(
        'System Notifications & Alerts',
        `<div style="display: flex; flex-direction: column; gap: 0.8rem;">
            <div style="padding: 0.75rem; background: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b;">
                <strong>Low Stock Warning</strong>
                <p style="font-size:0.85rem; color:#475569;">Seclo 20mg has only 12 units remaining in stock.</p>
            </div>
            <div style="padding: 0.75rem; background: #fee2e2; border-radius: 6px; border-left: 4px solid #ef4444;">
                <strong>Out of Stock Alert</strong>
                <p style="font-size:0.85rem; color:#475569;">Omeprazole 20mg (Renata) is currently out of stock.</p>
            </div>
            <div style="padding: 0.75rem; background: #f3e8ff; border-radius: 6px; border-left: 4px solid #8b5cf6;">
                <strong>Expired Medication</strong>
                <p style="font-size:0.85rem; color:#475569;">Ceevit 250mg Batch SQ-CV-102 reached expiry date on 2025-01-10.</p>
            </div>
        </div>`,
        `<button class="btn btn-primary" onclick="closeModal()">Acknowledge All</button>`
    );
}

/* ==========================================================================
   POS Interactive Register Functions
   ========================================================================== */

function addToPosCart(id, name, price, stock) {
    if (stock <= 0) {
        showToast('Item is Out of Stock!', 'danger');
        return;
    }

    const existingIndex = posCart.findIndex(item => item.id === id);
    if (existingIndex > -1) {
        if (posCart[existingIndex].qty + 1 > stock) {
            showToast('Cannot add more than available stock Qty!', 'warning');
            return;
        }
        posCart[existingIndex].qty += 1;
    } else {
        posCart.push({ id, name, price: parseFloat(price), qty: 1, stock });
    }

    renderPosCart();
    showToast(`Added ${name} to cart`, 'info');
}

function updateCartQty(id, change) {
    const item = posCart.find(i => i.id === id);
    if (!item) return;

    item.qty += change;
    if (item.qty <= 0) {
        removeFromCart(id);
        return;
    }
    if (item.qty > item.stock) {
        item.qty = item.stock;
        showToast('Maximum available stock limit reached', 'warning');
    }
    renderPosCart();
}

function removeFromCart(id) {
    posCart = posCart.filter(i => i.id !== id);
    renderPosCart();
}

function clearCart() {
    posCart = [];
    renderPosCart();
    showToast('Cart cleared', 'info');
}

function renderPosCart() {
    const cartContainer = document.getElementById('posCartItemsContainer');
    if (!cartContainer) return;

    if (posCart.length === 0) {
        cartContainer.innerHTML = `
            <div style="text-align: center; padding: 3rem 1rem; color: #94a3b8;">
                <i class="fa-solid fa-cart-shopping" style="font-size: 2.5rem; margin-bottom: 0.75rem;"></i>
                <p>No medicines selected for sale.</p>
                <span style="font-size: 0.8rem;">Click items on the left catalog to add to sale.</span>
            </div>
        `;
        updatePosTotals(0);
        return;
    }

    let html = '<table class="table" style="font-size:0.85rem;">';
    html += '<thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead><tbody>';

    let subtotal = 0;

    posCart.forEach(item => {
        const itemTotal = item.price * item.qty;
        subtotal += itemTotal;

        html += `
            <tr>
                <td><strong>${item.name}</strong></td>
                <td>$${item.price.toFixed(2)}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:0.3rem;">
                        <button class="btn btn-sm btn-outline" style="padding:0.1rem 0.4rem;" onclick="updateCartQty(${item.id}, -1)">-</button>
                        <span>${item.qty}</span>
                        <button class="btn btn-sm btn-outline" style="padding:0.1rem 0.4rem;" onclick="updateCartQty(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td><strong>$${itemTotal.toFixed(2)}</strong></td>
                <td><button class="btn btn-sm" style="color:#ef4444;" onclick="removeFromCart(${item.id})"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    cartContainer.innerHTML = html;
    updatePosTotals(subtotal);
}

function updatePosTotals(subtotal) {
    const discountInput = document.getElementById('posDiscountInput');
    const subtotalElem = document.getElementById('posSubtotal');
    const grandTotalElem = document.getElementById('posGrandTotal');

    let discountPercent = discountInput ? parseFloat(discountInput.value) || 0 : 0;
    let discountVal = (subtotal * discountPercent) / 100;
    let grandTotal = subtotal - discountVal;

    if (subtotalElem) subtotalElem.innerText = '$' + subtotal.toFixed(2);
    if (grandTotalElem) grandTotalElem.innerText = '$' + grandTotal.toFixed(2);
}

function processCompleteSale() {
    if (posCart.length === 0) {
        showToast('Please add items to cart before completing sale!', 'danger');
        return;
    }

    const customerName = document.getElementById('posCustomerName')?.value || 'Walk-in Customer';
    const paymentMethod = document.getElementById('posPaymentMethod')?.value || 'Cash';
    
    let subtotal = posCart.reduce((sum, i) => sum + (i.price * i.qty), 0);
    let discountPercent = parseFloat(document.getElementById('posDiscountInput')?.value) || 0;
    let discountVal = (subtotal * discountPercent) / 100;
    let grandTotal = subtotal - discountVal;
    
    const invoiceNo = 'INV-2026-' + Math.floor(100 + Math.random() * 900);
    const currentDate = new Date().toLocaleString();

    let receiptItemsHtml = '';
    posCart.forEach(item => {
        receiptItemsHtml += `
            <tr>
                <td>${item.name} x${item.qty}</td>
                <td>$${(item.price * item.qty).toFixed(2)}</td>
            </tr>
        `;
    });

    const thermalReceiptHtml = `
        <div class="thermal-receipt">
            <div class="receipt-header">
                <h3>PharmaCare Pro</h3>
                <p>University Medical Complex</p>
                <p>Hotline: +880 2-8833047</p>
                <hr>
                <p><strong>INVOICE: ${invoiceNo}</strong></p>
                <p>Date: ${currentDate}</p>
                <p>Customer: ${customerName}</p>
                <p>Payment Method: ${paymentMethod}</p>
            </div>
            <table class="receipt-table">
                <thead>
                    <tr><th>Item</th><th>Total</th></tr>
                </thead>
                <tbody>
                    ${receiptItemsHtml}
                </tbody>
            </table>
            <hr>
            <div style="display:flex; justify-content:space-between; font-size:11px;">
                <span>Subtotal:</span><span>$${subtotal.toFixed(2)}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:11px;">
                <span>Discount (${discountPercent}%):</span><span>-$${discountVal.toFixed(2)}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:13px; margin-top:4px;">
                <span>GRAND TOTAL:</span><span>$${grandTotal.toFixed(2)}</span>
            </div>
            <div class="receipt-footer">
                <p>Thank you for choosing PharmaCare!</p>
                <p>Get well soon.</p>
            </div>
        </div>
    `;

    openModal(
        'Sale Completed - Receipt Preview',
        thermalReceiptHtml,
        `<button class="btn btn-secondary" onclick="closeModal(); clearCart();">Close</button>
         <button class="btn btn-primary" onclick="window.print(); closeModal(); clearCart();"><i class="fa-solid fa-print"></i> Print Receipt</button>`
    );

    showToast('Sale completed successfully!', 'success');
}

// Demo Report Generation Launcher
function triggerReportDemo(reportTitle) {
    showToast(`Generating ${reportTitle}...`, 'info');
    setTimeout(() => {
        showToast(`${reportTitle} rendered successfully!`, 'success');
    }, 600);
}

function exportReportDemo(format) {
    showToast(`Exporting report as ${format}...`, 'info');
    setTimeout(() => {
        showToast(`Report downloaded successfully as Pharma_Report.${format.toLowerCase()}`, 'success');
    }, 800);
}
