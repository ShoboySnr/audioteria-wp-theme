const menuButton = document.querySelector('.account-menu');
let isOpened = false;
menuButton.addEventListener('click', () => {
  if (isOpened) {
    document.querySelector('.account-links-wrapper').style.display = 'none';
    document.querySelector('.account-menu .open').style.display = 'block';
    document.querySelector('.account-menu .close').style.display = 'none';
    menuButton.style.top = '-68px';
    isOpened = !isOpened;
  } else {
    document.querySelector('.account-links-wrapper').style.display = 'block';
    document.querySelector('.account-menu .open').style.display = 'none';
    document.querySelector('.account-menu .close').style.display = 'block';
    menuButton.style.top = '-75px';
    isOpened = !isOpened;
  }
});

// STAR RATING
const rateProductBtn = document.querySelectorAll('.order-rate');
const rateCancels = document.querySelectorAll('.rate-cancel');
const rateModals = document.querySelectorAll('.rate-modal');
// RATE PRODUCT BUTTONS
rateProductBtn.forEach((button, index) => {
  button.addEventListener('click', () => {
    rateModals[index].style.display = 'flex';
  });
  // CANCEL BUTTONS
  rateCancels[index].addEventListener('click', () => {
    rateModals[index].style.display = 'none';
  });
});

const stars = document.querySelectorAll('.rate-stars svg');
const starWrappers = document.querySelectorAll('.rate-stars');

const handleStarClick = (starWrapper, wrapperIndex) => {
  const stars = starWrapper.querySelectorAll('svg');
  const starInputs = document.querySelectorAll('.rate-container input');
  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      stars.forEach((star, starIndex) => {
        if (starIndex <= index) {
          star.classList.add('star-fill');
        } else {
          star.classList.remove('star-fill');
        }
      });
      starInputs[wrapperIndex].setAttribute('value', index + 1);
    });
  });
};

starWrappers.forEach((starWrapper, index) => {
  handleStarClick(starWrapper, index);
});
