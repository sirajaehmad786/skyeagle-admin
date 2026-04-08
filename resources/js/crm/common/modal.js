export default (function(){

    function openModal(id){
        
        const modalEl = document.getElementById(id);
        if(modalEl){
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function closeModal(id){
        const modalEl = document.getElementById(id);
        if(modalEl){
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        }
    }
    function init(){
        document.addEventListener('click', (e) => {
            const openBtn = e.target.closest('[data-open-modal]');
            const closeBtn = e.target.closest('[data-close-modal]');

            if (openBtn) {
                openModal(openBtn.dataset.openModal);
            }

            if(closeBtn){
                closeModal(closeBtn.dataset.closeModal);
            }
        })
    }

    return {init};
})();