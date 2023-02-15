window.addEventListener('DOMContentLoaded', () => {
  premierSlider();
  categorySlider('sci-Fi');
  categorySlider('drama');
  categorySlider('horror');
  window.addEventListener('resize', () => {
    premierSlider();
    categorySlider('sci-Fi');
    categorySlider('drama');
    categorySlider('horror');
  });
});

const carouselItems = document.querySelectorAll('.carousel-item');
const pointerContainer = document.querySelector('.home-heading-pointer');
const newPointer = (index) =>
  `<li data-target="#carouselExampleIndicators" data-slide-to="${index}" ${
    index === 0 ? 'class="active"' : ''
  }></li>`;
pointerContainer.innerHTML = '';
carouselItems.forEach((item, index) => {
  pointerContainer.innerHTML += newPointer(index);
});

// tagsContainer.innerHTML += createTag(text);
const premierSlider = () => {
  const leftArrow = document.querySelector('.premier-releases .left-arrow');
  const rightArrow = document.querySelector('.premier-releases .right-arrow');
  const wrapper = document.querySelector('.premier-releases-wrapper');
  const item = document.querySelectorAll('.premier-releases-wrapper-item');

  let translate = 0;
  let noOfCardsOnScreen = 3;
  if (window.innerWidth < 640) {
    noOfCardsOnScreen = 1;
  }

  // Set listeners and attributes to defaults
  leftArrow.addEventListener('click', null);
  rightArrow.addEventListener('click', null);
  wrapper.setAttribute('style', `transform: translateX(0%)`);
  rightArrow.setAttribute('style', 'opacity: 1');
  leftArrow.setAttribute('style', 'opacity: 0.5');
  const parentContainer = document.querySelector('.premier-releases-container');

  leftArrow.addEventListener('click', () => {
    if (translate > 0) {
      translate -= 100;
      wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
      rightArrow.setAttribute('style', 'opacity: 1');
      parentContainer.classList.add('premier-releases-container-after');
      if (translate <= 0) {
        leftArrow.setAttribute('style', 'opacity: 0.5');
        parentContainer.classList.remove('premier-releases-container-before');
      }
    }
  });
  rightArrow.addEventListener('click', () => {
    if (noOfCardsOnScreen === 1) {
      if (translate < (item.length / noOfCardsOnScreen - 1) * 100) {
        parentContainer.classList.add('premier-releases-container-before');
        translate += 100;
        wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
        leftArrow.setAttribute('style', 'opacity: 1');
        if (translate > (item.length / noOfCardsOnScreen - 1) * 100) {
          rightArrow.setAttribute('style', 'opacity: 0.5');
          parentContainer.classList.remove('premier-releases-container-after');
        }
      }
    } else if (translate <= (item.length / noOfCardsOnScreen - 1) * 100) {
      parentContainer.classList.add('premier-releases-container-before');
      translate += 100;
      wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
      leftArrow.setAttribute('style', 'opacity: 1');
      if (translate > (item.length / noOfCardsOnScreen - 1) * 100) {
        rightArrow.setAttribute('style', 'opacity: 0.5');
        parentContainer.classList.remove('premier-releases-container-after');
      }
    }
  });
};

const getNoOfCardsOnScreen = () => {
  const card = document.querySelector('.category-wrapper-item').clientWidth;
  const container = document.querySelector('.category-wrapper').clientWidth;

  return Math.floor(container / card);
};

const categorySlider = (category) => {
  let noOfCardsOnScreen = getNoOfCardsOnScreen();
  const parentContainer = document.querySelector(
    `.${category} .category-container`
  );
  const leftArrow = document.querySelector(`.${category} .left-arrow`);
  const rightArrow = document.querySelector(`.${category} .right-arrow`);
  const wrapper = document.querySelector(`.${category} .category-wrapper`);
  const bookCards = document.querySelectorAll(
    `.${category} .category-wrapper-item`
  );
  let translate = 0;

  // Set listeners and attributes to defaults
  leftArrow.addEventListener('click', null);
  rightArrow.addEventListener('click', null);
  wrapper.setAttribute('style', `transform: translateX(0%)`);
  rightArrow.setAttribute('style', 'opacity: 1');
  leftArrow.setAttribute('style', 'opacity: 0.5');

  leftArrow.addEventListener('click', () => {
    if (translate > 0) {
      translate -= 100;
      wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
      rightArrow.setAttribute('style', 'opacity: 1');
      parentContainer.classList.add('category-container-after');
      if (translate <= 0) {
        leftArrow.setAttribute('style', 'opacity: 0.5');
        parentContainer.classList.remove('category-container-before');
      }
    }
  });
  rightArrow.addEventListener('click', () => {
    if (noOfCardsOnScreen === 1) {
      if (translate < (bookCards.length / noOfCardsOnScreen - 1) * 100) {
        translate += 100;
        parentContainer.classList.add('category-container-before');
        wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
        leftArrow.setAttribute('style', 'opacity: 1');
        if (translate > (bookCards.length / noOfCardsOnScreen - 1) * 100) {
          rightArrow.setAttribute('style', 'opacity: 0.5');
          parentContainer.classList.remove('category-container-after');
        }
      }
    } else if (translate <= (bookCards.length / noOfCardsOnScreen - 1) * 100) {
      parentContainer.classList.add('category-container-before');
      translate += 100;
      wrapper.setAttribute('style', `transform: translateX(-${translate}%)`);
      leftArrow.setAttribute('style', 'opacity: 1');
      if (translate > (bookCards.length / noOfCardsOnScreen - 1) * 100) {
        rightArrow.setAttribute('style', 'opacity: 0.5');
        parentContainer.classList.remove('category-container-after');
      }
    }
  });
};
