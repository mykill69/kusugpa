// public/js/app-functions.js
function openUploadModal(type) {
    const titles = {
        'summary': 'Upload Summary CSV',
        'trucking': 'Upload Trucking Allowance CSV',
        'fci': 'Upload Fresh Cane Incentive CSV',
        'fuel': 'Upload Fuel CSV',
        'rentals': 'Upload Rentals CSV',
        'underload': 'Upload Underload CSV',
        'transloading': 'Upload Transloading CSV'
    };
    
    Swal.fire({
        title: titles[type] || 'Upload CSV',
        html: `
            <form id="uploadForm" method="POST" action="/upload/${type}" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                    <input type="file" name="file" accept=".csv,.txt" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" required>
                    <p class="mt-2 text-xs text-gray-500">Accepted formats: .csv, .txt (Max size: 5MB)</p>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        preConfirm: () => {
            const form = document.getElementById('uploadForm');
            const fileInput = form.querySelector('input[type="file"]');
            
            if (!fileInput.files.length) {
                Swal.showValidationMessage('Please select a file');
                return false;
            }
            
            form.submit();
        }
    });
}

// Add other functions similarly...