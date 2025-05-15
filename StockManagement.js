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
    const addModalCancel = document.getElementById('addModalCancel');
    const deleteModalNo = document.getElementById('confirmDeleteNo');
    const editModalCancel = document.getElementById('editModalCancel');

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
    addModalCancel.addEventListener('click', () => {
      document.getElementById('addModal').style.display = 'none';
    });

    window.addEventListener('click', e => { 
        if (e.target === addModal) addModal.style.display = 'none'; 
        if (e.target === deleteModal) deleteModal.style.display = 'none'; 
        if (e.target === editModal) editModal.style.display = 'none'; 
    });

    document.querySelectorAll('a.delete').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const vid = link.dataset.variationId;
        console.log('Preparing to delete variationID=', vid);
        deleteModal.dataset.variationId = vid;
        deleteModal.style.display = 'flex';
      });
    });

    // Confirm no
    deleteModalNo.addEventListener('click', () => {
      document.getElementById('addModal').style.display = 'none';
    });

    // Confirm yes
    confirmDeleteYes.addEventListener('click', () => {
      const vid = deleteModal.dataset.variationId;
      window.location.href = `?delete=${vid}&confirm=yes`;
    });

    document.querySelectorAll('a.edit').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const json = link.dataset.product;

        try {
          const product = JSON.parse(json);

          document.getElementById('editVariationID').value     = product.variationID || '';
          document.getElementById('editName').value            = product.product_name || '';
          document.getElementById('editDescription').value     = product.description || '';
          document.getElementById('editSize').value            = product.size || '';
          document.getElementById('editPrice').value           = product.price || '';
          document.getElementById('editQty').value             = product.quantity_on_hand || '';
          document.getElementById('editReorder').value         = product.reorder_level || '';

          editModal.style.display = 'flex';
        } catch (err) {
          console.error('Failed to parse product JSON:', err, json);
        }
      });
    });

    editModalCancel.addEventListener('click', () => {
      document.getElementById('editModal').style.display = 'none';
    });

    filterTable();
});