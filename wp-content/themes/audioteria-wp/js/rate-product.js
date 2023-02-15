function RateProduct() {}

  RateProduct.openModal = (elem) => {
    elem.addEventListener('click', function (ev) {
      ev.preventDefault();
      ev.stopPropagation();

      const nextSib =  elem.nextElementSibling;
      nextSib.removeAttribute('class');
      nextSib.classList.add('four');
      document.querySelector('body').style.overflow = 'hidden';
    });
  }

  RateProduct.closeModal = (elem) => {
    elem.addEventListener('click', function (ev) {
      ev.preventDefault();
      ev.stopPropagation();

      const rateModalContent = elem.parentElement;
      const rateModalBg = rateModalContent.parentElement;
      const rateModal = rateModalBg.parentElement;

      rateModal.classList.remove('four');
      document.querySelector('body').style.overflow = 'auto';
    });
  };

  RateProduct.bgCloseModal = (elem) => {
    elem.addEventListener('click', function (ev) {
      if (ev.target === elem){
        ev.preventDefault();
        ev.stopPropagation();
        const rateModal = elem.parentElement;

        rateModal.classList.remove('four');
        document.querySelector('body').style.overflow = 'auto';
      }
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.rate-button').forEach(function(nl){
      RateProduct.openModal(nl);
    });

    document.querySelectorAll('#trailer-modal #close').forEach(function(ml){
      RateProduct.closeModal(ml);
    });

    document.querySelectorAll('#trailer-modal .trailer-modal-background').forEach(function(ol){
      RateProduct.bgCloseModal(ol);
    });

  });
