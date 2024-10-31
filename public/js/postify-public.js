var sliderSelector = ".swiper-container",
  defaultOptions = {
    breakpointsInverse: true,
    observer: true,
  };
var nSlider = document.querySelectorAll(sliderSelector);
[].forEach.call(nSlider, function (slider, index, arr) {
  var data = slider.getAttribute("data-swiper") || {};

  if (data) {
    var dataOptions = JSON.parse(data);
  }
  slider.options = Object.assign({}, defaultOptions, dataOptions);
  var swiper = new Swiper(slider, slider.options);
  /* stop on hover */
  if (
    typeof slider.options.autoplay !== "undefined" &&
    slider.options.autoplay !== false
  ) {
    slider.addEventListener("mouseenter", function (e) {
      swiper.autoplay.stop();
    });

    slider.addEventListener("mouseleave", function (e) {
      swiper.autoplay.start();
    });
  }
});
