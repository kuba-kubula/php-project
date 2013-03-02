//================================================================================================= GLITCH

var NR_OF_GLITCHED_CANVASES = 5;
var TOTAL_TIMES_TO_RENDER   = 2;
var GLITCH_FORCE            = 5;
var GLITCH_NEXT_FRAME_DELAY = 120;
var GLITCH_NEXT_FRAME_DELAY_COMPUTED = 120;

var rendered_canvases = 0;
var times_rendered    = 0;
var glitched_canvases = Array();
var curr_canvas = null;
var bdy = null;
var started = false;
var ended = false;
var render_glitches_timer = null;

function getRandomInt (min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function do_glitch() {
  if (!ended) {
    bdy = document.getElementById('projection');
    var cnvs = bdy.querySelectorAll('canvas.inv, canvas.vis');
    for (var i = cnvs.length - 1; i >= 0; i--) {
      cnvs[i].setAttribute('class', 'inv');
    }
    glitch(bdy, {
      amount: getRandomInt(GLITCH_FORCE - 2, GLITCH_FORCE + 2),
      proxy: false,
      complete: function(canvas) {
        if (!ended) {
          if (started) {
            if (rendered_canvases < NR_OF_GLITCHED_CANVASES) {
              glitched_canvases[rendered_canvases].getContext('2d').drawImage(canvas, 0, 0);
              glitched_canvases[rendered_canvases].setAttribute('class', 'inv');
              rendered_canvases++;
            }
          }
          else {
            glitched_canvases.push(canvas);
            canvas.setAttribute('class', 'inv');
            bdy.appendChild(canvas);
          }
          if ((!started && glitched_canvases.length < NR_OF_GLITCHED_CANVASES) || (started && rendered_canvases < NR_OF_GLITCHED_CANVASES)) {
            clearTimeout(render_glitches_timer);
            render_glitches_timer = setTimeout(do_glitch, 20);
          }
          else {
            if (!started) {
              started = true;
            }
            rendered_canvases = 0;
            render_glitches();
          }
        }
      }
    });
  }
  else {
  }
}

function restart_glitch() {
  if (!started) {
    ended = false;
    clearTimeout(render_glitches_timer);
    render_glitches_timer = setTimeout(do_glitch, 300);
  }
  else {
    ended = true;
    clearTimeout(render_glitches_timer);
    render_glitches_timer = setTimeout(render_glitches, 900);
  }
}

function render_glitches() {
  if (!ended && times_rendered < TOTAL_TIMES_TO_RENDER) {
    if (curr_canvas != null) {
      curr_canvas.setAttribute('class', 'inv');
    }
    if (!ended && (0 < glitched_canvases.length) && (rendered_canvases < glitched_canvases.length)) {
      curr_canvas = glitched_canvases[rendered_canvases];
      curr_canvas.setAttribute('class', 'vis');
      rendered_canvases++;
      clearTimeout(render_glitches_timer);
      render_glitches_timer = setTimeout(render_glitches, GLITCH_NEXT_FRAME_DELAY_COMPUTED);
    }
    else if (!ended) {
      curr_canvas.setAttribute('class', 'inv');
      if (rendered_canvases >= glitched_canvases.length) {
        times_rendered++;
      }
      rendered_canvases = 0;
      clearTimeout(render_glitches_timer);
      render_glitches_timer = setTimeout(render_glitches, getRandomInt(2500, 3700));
      curr_canvas = null;
    }
  }
  else {
    rendered_canvases = 0;
    times_rendered = 0;
    if (curr_canvas != null) {
      curr_canvas.setAttribute('class', 'inv');
      curr_canvas = null;
    }
    ended = false;
    GLITCH_NEXT_FRAME_DELAY_COMPUTED = getRandomInt(GLITCH_NEXT_FRAME_DELAY - 20, GLITCH_NEXT_FRAME_DELAY + 30);
    do_glitch();
  }
}

//================================================================================================= END

function draw_small_hexagon(ctxDSH, Xcenter, Ycenter, size, fillStyle) {
  ctxDSH.globalAlpha = 0.8;
  ctxDSH.beginPath();
  ctxDSH.moveTo (Xcenter +  size * Math.cos(0), Ycenter +  size *  Math.sin(0));
  for (var i = 1; i <= 6; i += 1) {
    ctxDSH.lineTo (Xcenter + size * Math.cos(i * 2 * Math.PI / 6), Ycenter + size * Math.sin(i * 2 * Math.PI / 6));
  }
  ctxDSH.fillStyle = fillStyle;
  ctxDSH.fill();
  ctxDSH.globalAlpha = 1.0;
}

function draw_large_hexagon(ctxDLH, Xcenter, Ycenter, size, fillStyle) {
  // hexagon
  ctxDLH.globalAlpha = 1.0;
  ctxDLH.beginPath();
  ctxDLH.moveTo (Xcenter +  size * Math.cos(0.5), Ycenter +  size *  Math.sin(0.5));
  for (var i = 1; i <= 6; i += 1) {
    ctxDLH.lineTo (Xcenter + size * Math.cos((i+0.5) * 2 * Math.PI / 6), Ycenter + size * Math.sin((i+0.5) * 2 * Math.PI / 6));
  }
  ctxDLH.fillStyle = fillStyle;
  ctxDLH.fill();
  ctxDLH.globalAlpha = 1.0;
}

var getPosition = function( svgEl, childEl ) {
  // Get the bounding box of a child element within the SVG svgEl.
  // Values are represented as fractions of width/height.

  var bBoxes = [],
      current = childEl,
      nodeName;

  while ( true ) {
    nodeName = current ? (current.nodeName && current.nodeName.toLowerCase()) : false;
    if ( current !== svgEl ) {
      if (current && nodeName == 'text') {
        bBoxes.push( { transform: [current.getAttribute('x'), current.getAttribute('y')] });
      }
      else if (nodeName == false) {
        bBoxes.push({});
      }
      else if (nodeName == 'g' || nodeName == 'polygon' || nodeName == 'line') {
        bBoxes.push({ transform: (!!current.getAttribute('transform') ? current.getAttribute('transform').replace(/[\s]g/, '').replace('translate(', '').replace(')', '').split(',') : [0, 0]) });
      }
    } else {
      bBoxes.push({ transform: [0, 0] });

      break;
    }

    current = current.parentNode;

    if ( !current ) {
      throw new Error( "Element must be descendant of SVG." );
      break;
    }
  }

  var i, l,
      totalBox = {
        x: 0,
        y: 0
      };

  for ( i = 0, l = bBoxes.length; i < l; i++ ) {
    current = bBoxes[ i ];
    totalBox.x += parseFloat(current.transform[0]);
    totalBox.y += parseFloat(current.transform[1]);
  }

  return {
    x: parseInt(totalBox.x, 10),
    y: parseInt(totalBox.y, 10)
  };
};

var renderTexts = function (ctxRT, texts, svg) {
  for (var i = 0, l = texts.length; i < l; i++) {
    var one = texts.item(i);
    var pos = getPosition(svg, one);
    ctxRT.font = 'normal ' + (one.style.fontSize || '20px') + ' Omikron, omikron-webfont';
    ctxRT.fillStyle = '#000';
    ctxRT.textAlign = 'left';
    ctxRT.textBaseline = 'middle';
    ctxRT.fillStyle = '#000';
    ctxRT.fillText(one.firstChild.data, pos.x, pos.y);
  }
}

var renderLines = function (ctxRL, lines, svg) {
  for (var i = 0, l = lines.length; i < l; i++) {
    var itm = lines.item(i);
    var pos = getPosition(svg, itm);
    ctxRL.beginPath();
    ctxRL.strokeStyle = 'rgba(9,241,189, 0.5)';
    ctxRL.lineWidth = 5;
    ctxRL.moveTo(pos.x + 1*itm.getAttribute('x1'), pos.y + 1*itm.getAttribute('y1'));
    ctxRL.lineTo(pos.x + 1*itm.getAttribute('x2'), pos.y + 1*itm.getAttribute('y2'));
    ctxRL.stroke();
  }
}

var renderPolys = function (ctxRP, polys, svg) {
  for (var i = 0, l = polys.length; i < l; i++) {
    var itm = polys.item(i);
    var pos = getPosition(svg, itm);
    if (itm.getAttribute('stroke-width') == '2') {
      draw_small_hexagon(ctxRP, pos.x, pos.y, 15, itm.getAttribute('fill'));
    }
    else {
      draw_large_hexagon(ctxRP, pos.x, pos.y, 35, itm.getAttribute('fill'));
    }
  }
}

var renderImages = function (ctxRI, imgs, svg) {
  for (var i = 0; i < imgs; i++) {
    var im = imgs.item(i);
    var pos = getPosition(svg, im);
    var aa;
    if (aa = document.querySelector('img.' + im.getAttribute('xlink:href').replace(/[^a-zA-Z0-9]/g,''))) {
      ctxRI.drawImage(aa, (1*pos.x - 31), (1*pos.y - 35));
    }
  }
}
function svgDataURL(svg) {
  var svgAsXML = (new XMLSerializer).serializeToString(svg);
  return "data:image/svg+xml," + encodeURIComponent(svgAsXML);
}

function enterFullscreen() {
  var element = document.getElementById("projection");
  // Check which implementation is available
  var requestMethod = element.requestFullScreen ||
                      element.webkitRequestFullScreen ||
                      element.mozRequestFullScreen ||
                      element.msRequestFullScreen;
  if (requestMethod) {
    requestMethod.apply( element );
  }
}

function onDocumentKeyDown (event) {
  if (event.keyCode == 70) {
    enterFullscreen();
  }
}
document.addEventListener( 'keydown', onDocumentKeyDown, false );
