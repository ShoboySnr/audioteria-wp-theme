function Productjs() {};

Productjs.playAudio = () => {
    const trailerOpener = document.getElementById("play-trailer");

    if(trailerOpener !== null){
        trailerOpener.addEventListener('click', () => {
            const closeBtn = document.querySelector("#trailer-modal .close");
            if (closeBtn !== null) {
                const pauseTrailerBtn = document.querySelector("#trailer-audio .mejs-pause button");
                closeBtn.addEventListener('click', () => {
                    if (pauseTrailerBtn !== false) {
                        document.querySelector("#trailer-audio .mejs-pause button").click();
                    }
                });
            }
        });
    }
};

Productjs.toggleSharebox = () => {
    const shareToggle = document.getElementById('share-toggle');
    const shareBox = document.getElementById('share-box');
    const closebox = document.getElementById('close-share');

    shareToggle.addEventListener('click', function () {
      shareBox.classList.toggle("box-open");
    });
    closebox.addEventListener('click', function () {
      shareBox.classList.toggle("box-open");
    });
}

window.addEventListener('DOMContentLoaded', () => {
  Productjs.playAudio();
  Productjs.toggleSharebox();
});
