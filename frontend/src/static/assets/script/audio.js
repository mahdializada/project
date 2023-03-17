initailizeAudio = function (url) {
  const audio = new Audio(url);
  audio.classList.add("w-full");
  audio.style.borderRadius = '20px';
  audio.controls = true;

  var tag = document.querySelector("#preview-audio-id");
  tag.innerHTML = "";
  tag.appendChild(audio);
  audio.load();

  var context = new AudioContext();
  var src = context.createMediaElementSource(audio);
  var analyser = context.createAnalyser();
  var canvas = document.getElementById("audiocanvas");
  var ctx = canvas.getContext("2d");

  src.connect(analyser);
  analyser.connect(context.destination);
  analyser.fftSize = 256;
  var bufferLength = analyser.frequencyBinCount;
  var dataArray = new Uint8Array(bufferLength);

  var WIDTH = canvas.width;
  var HEIGHT = canvas.height;

  var barWidth =  ~~((WIDTH / bufferLength) * 2.5);
  var barHeight;
  var x = 0;

  function renderFrame() {
    requestAnimationFrame(renderFrame);
    x = 0;
    analyser.getByteFrequencyData(dataArray);

    ctx.fillStyle = "#000";
    ctx.fillRect(0, 0, WIDTH, HEIGHT);

    for (var i = 0; i < bufferLength; i++) {
      barHeight = ~~(dataArray[i] / 2);
      
      var r = dataArray[i] + (25 * (i/bufferLength));
      var g = 250 * (i/bufferLength);
      var b = 50;

      ctx.fillStyle = "rgb(" + r + "," + g + "," + b + ")";
      ctx.fillRect(x, HEIGHT - barHeight, barWidth, barHeight);

      x += barWidth + 1;
    }
  }

  audio.play();
  renderFrame();
};
