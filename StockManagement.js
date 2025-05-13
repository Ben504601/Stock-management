function toggledropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.closest(".userprofile")) {
        document.getElementById("dropdown").classList.remove("show");
    }
}

document.addEventListener('DOMContentLoaded', () => {
  // for search bar & filters
  const searchInput   = document.getElementById('searchInput');
  const sizeFilter    = document.getElementById('sizeFilter');
  const statusFilter  = document.getElementById('statusFilter');
  const tableBody     = document.querySelector('.stock-management table tbody');
  // updating boxes
  const totalBox = document.querySelector('#totalItemsBox .number');
  const lowBox   = document.querySelector('#lowStockBox .number');
  const outBox   = document.querySelector('#outStockBox .number');

  searchInput.addEventListener('input', filterTable);

  // whenever any filter changes, re‑run filterTable()
  [ sizeFilter, statusFilter ].forEach(el =>
    el.addEventListener('change', filterTable)
  );

  function filterTable() {
    const searchText = searchInput.value.trim().toLowerCase();
    const sizeValue  = sizeFilter.value;
    const statusValue= statusFilter.value;

    Array.from(tableBody.rows).forEach(row => {
      const nameCell   = row.cells[0].textContent.toLowerCase();
      const sizeCell   = row.cells[1].textContent;
      const statusSpan = row.cells[5].querySelector('span').textContent;

      // check each criteria
      const matchesSearch = nameCell.includes(searchText);
      const matchesSize   = (sizeValue === 'all') || (sizeCell === sizeValue);
      const matchesStatus = (statusValue === 'all') || (statusSpan === statusValue);

      // only show if all match
      row.style.display = (matchesSearch && matchesSize && matchesStatus)
                          ? ''  // show
                          : 'none';  // hide
    });
    updateCounters();
  }

  function updateCounters() {
    const rows = Array.from(tableBody.rows)
                      .filter(r => r.style.display !== 'none');
    const totalCount = rows.length;
    let lowCount = 0, outCount = 0;

    rows.forEach(r => {
      const status = r.cells[5].querySelector('span').textContent;
      if(status === 'Low Stock')  lowCount++;
      if(status === 'No Stock')   outCount++;
    });
    totalBox.textContent = totalCount;
    lowBox.textContent   = lowCount;
    outBox.textContent   = outCount;
  }

  // open/close modal
  const addBtn   = document.getElementById('addBtn');
const modal    = document.getElementById('addModal');
const closeBtn = document.getElementById('modalClose');

addBtn.addEventListener('click', () => {
  modal.style.display = 'flex';
});

// Close when you click the “×”
closeBtn.addEventListener('click', () => {
  modal.style.display = 'none';
});

// Close when you click outside the white box
window.addEventListener('click', e => {
  if (e.target === modal) {
    modal.style.display = 'none';
  }

  // initialize on load
  filterTable();
});
  