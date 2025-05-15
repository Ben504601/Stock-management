console.log("StockManagement.js loaded");

function toggledropdown() {
    console.log("toggledropdown called");
    document.getElementById("dropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.closest(".userprofile")) {
        document.getElementById("dropdown").classList.remove("show");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const sizeFilter = document.getElementById('sizeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.querySelector('.stock-management table tbody');
    const totalBox = document.querySelector('#totalItemsBox .number');
    const lowBox = document.querySelector('#lowStockBox .number');
    const outBox = document.querySelector('#outStockBox .number');
    const addBtn = document.getElementById('addBtn');
    const addModal = document.getElementById('addModal');
    const addModalClose = document.getElementById('addModalClose');
    const deleteModal = document.getElementById('deleteModal');
    const deleteModalClose = document.getElementById('deleteModalClose');
    const confirmDeleteYes = document.getElementById('confirmDeleteYes');
    const confirmDeleteNo = document.getElementById('confirmDeleteNo');
    const editModal = document.getElementById('editModal');
    const editModalClose = document.getElementById('editModalClose');
    const editConfirmModal = document.getElementById('editConfirmModal');
    const editConfirmModalClose = document.getElementById('editConfirmModalClose');
    const confirmEditYes = document.getElementById('confirmEditYes');
    const confirmEditNo = document.getElementById('confirmEditNo');

    if (!searchInput || !sizeFilter || !statusFilter || !tableBody || !totalBox || !lowBox || !outBox || !addBtn || !addModal || !addModalClose || !deleteModal || !deleteModalClose || !confirmDeleteYes || !confirmDeleteNo || !editModal || !editModalClose || !editConfirmModal || !editConfirmModalClose || !confirmEditYes || !confirmEditNo) {
        console.error('One or more DOM elements not found:', {
            searchInput, sizeFilter, statusFilter, tableBody, totalBox, lowBox, outBox, addBtn, addModal, addModalClose,
            deleteModal, deleteModalClose, confirmDeleteYes, confirmDeleteNo, editModal, editModalClose,
            editConfirmModal, editConfirmModalClose, confirmEditYes, confirmEditNo
        });
        return;
    }

    console.log('All DOM elements found, initializing event listeners...');

    searchInput.addEventListener('input', filterTable);
    [sizeFilter, statusFilter].forEach(el => el.addEventListener('change', filterTable));

    function filterTable() {
        const searchText = searchInput.value.trim().toLowerCase();
        const sizeValue = sizeFilter.value;
        const statusValue = statusFilter.value;

        Array.from(tableBody.rows).forEach(row => {
            const nameCell = row.cells[0].textContent.toLowerCase();
            const sizeCell = row.cells[1].textContent;
            const statusSpan = row.cells[5].querySelector('span').textContent;

            const matchesSearch = nameCell.includes(searchText);
            const matchesSize = (sizeValue === 'all') || (sizeCell === sizeValue);
            const matchesStatus = (statusValue === 'all') || (statusSpan === statusValue);

            row.style.display = (matchesSearch && matchesSize && matchesStatus) ? '' : 'none';
        });
        updateCounters();
    }

    function updateCounters() {
        const rows = Array.from(tableBody.rows).filter(r => r.style.display !== 'none');
        const totalCount = rows.length;
        let lowCount = 0, outCount = 0;

        rows.forEach(r => {
            const status = r.cells[5].querySelector('span').textContent;
            if (status === 'Low Stock') lowCount++;
            if (status === 'No Stock') outCount++;
        });
        totalBox.textContent = totalCount;
        lowBox.textContent = lowCount;
        outBox.textContent = outCount;
    }

    addBtn.addEventListener('click', () => { addModal.style.display = 'flex'; });
    addModalClose.addEventListener('click', () => { addModal.style.display = 'none'; });

    window.addEventListener('click', e => { 
        if (e.target === addModal) addModal.style.display = 'none'; 
        if (e.target === deleteModal) deleteModal.style.display = 'none'; 
        if (e.target === editModal) editModal.style.display = 'none'; 
        if (e.target === editConfirmModal) editConfirmModal.style.display = 'none'; 
    });

    deleteModalClose.addEventListener('click', () => { deleteModal.style.display = 'none'; });
    confirmDeleteNo.addEventListener('click', () => { deleteModal.style.display = 'none'; });
    confirmDeleteYes.addEventListener('click', () => {
        const vid = deleteModal.dataset.variationId;
        window.location.href = `?delete=${vid}&confirm=yes`;
    });

    editModalClose.addEventListener('click', () => { editModal.style.display = 'none'; });
    editConfirmModalClose.addEventListener('click', () => { editConfirmModal.style.display = 'none'; });
    confirmEditNo.addEventListener('click', () => { editConfirmModal.style.display = 'none'; });

    let currentProduct = null;

    confirmEditYes.addEventListener('click', () => {
        editConfirmModal.style.display = 'none';
        if (currentProduct) {
            try {
                console.log('Proceeding to edit product:', currentProduct);
                document.getElementById('editVariationID').value = currentProduct.variationID || '';
                document.getElementById('editName').value = currentProduct.product_name || '';
                document.getElementById('editDescription').value = currentProduct.description || '';
                document.getElementById('editSize').value = currentProduct.size || '';
                document.getElementById('editPrice').value = currentProduct.price || '';
                document.getElementById('editQty').value = currentProduct.quantity_on_hand || '';
                document.getElementById('editReorder').value = currentProduct.reorder_level || '';
                editModal.style.display = 'flex';
            } catch (e) {
                console.error('Error in confirmEditYes:', e);
            }
        }
    });

    window.showDeleteModal = function(vid) {
        console.log('Showing delete modal for vid:', vid);
        deleteModal.dataset.variationId = vid;
        deleteModal.style.display = 'flex';
    };

    window.editProduct = function(productJson) {
        try {
            console.log('Edit button clicked with JSON:', productJson);
            const product = JSON.parse(productJson);
            if (product && typeof product === 'object') {
                currentProduct = product;
                editConfirmModal.style.display = 'flex';
            } else {
                console.error('Invalid product data after parsing:', product);
            }
        } catch (e) {
            console.error('Error in editProduct:', e);
        }
    };

    filterTable();
});