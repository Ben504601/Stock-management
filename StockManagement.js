function toggledropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.closest(".userprofile")) {
        document.getElementById("dropdown").classList.remove("show");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const searchInput   = document.getElementById('searchInput');
    const sizeFilter    = document.getElementById('sizeFilter');
    const statusFilter  = document.getElementById('statusFilter');
    const tableBody     = document.querySelector('.stock-management table tbody');

    searchInput.addEventListener('keydown', e => {
        if(e.key === 'Enter') {
            e.preventDefault();
            filterTable();
        }
    });
  
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
        const statusSpan = row.cells[4].querySelector('span').textContent;
  
        // check each criterion
        const matchesSearch = nameCell.includes(searchText);
        const matchesSize   = (sizeValue === 'all') || (sizeCell === sizeValue);
        const matchesStatus = (statusValue === 'all') || (statusSpan === statusValue);
  
        // only show if all match
        row.style.display = (matchesSearch && matchesSize && matchesStatus)
                            ? ''  // show
                            : 'none';  // hide
      });
    }
  
    // initialize on load
    filterTable();
  });
  