document.addEventListener('DOMContentLoaded', (event) => {
    let toastBox = document.getElementById('toastbox');

    function showToast(msg) {
        let toast = document.createElement('div');
        toast.classList.add('toast');
        toast.innerHTML = msg;
        toastBox.appendChild(toast);

        if (msg.includes('Error') || msg.includes('Incorrect')) {
            toast.classList.add('error');
        }
        if (msg.includes('Invalid') || msg.includes('already')) {
            toast.classList.add('invalid');
        }

        setTimeout(() => {
            toast.remove();
        }, 2500);
    }

    window.showToast = showToast; // Expose the function to the global scope
});

