var NAME_SCREEN = 1; ///< Identifier of the screen with the name prompt.
var PASS_SCREEN = 2; ///< Identifier of the screen with the password prompt.
var TEXT_SCREEN = 3; ///< Identifier of the screen with the text content.

// the coolest part of it - it is a webpage, you know?
var ALPHA_BEGIN = 65;
var ALPHA_END = 91;
var NUM_BEGIN = 48;
var NUM_END = 57;
var SPACE = '_'; // In the keyboard space is replaced with _ for clarity.

// Dimensions, in the number of buttons, of the keyboard.
var BOARD_WIDTH = 9;
var BOARD_HEIGHT = 3;

// Strings for some action buttons.
var CONFIRM = "Potvrd";
var ERASE = "Smaz";
var KILL = "Zrus";
var FIRST = "<<";
var PREV = "<";
var NEXT = ">";
var LAST = ">>";
var buttonsCoordinates = [];
var btnId = 0;
var data;
var http;
var environment;

function Button (text, left, top, width, height, className) {
  var el;
  btnId++;
  el = jQuery("<span id='" + "ui_btn_" + btnId + "' class='button'>"+ text +"</span>").css({'left': left, 'top': top, 'height': height, 'width': width})
  if (className) {
    el.addClass(className);
  }
  buttonsCoordinates.push(["ui_btn_" + btnId, left, top, left+width, top+height, el]);
  return el;
}

function enterFullscreen() {
  var element = document.body;
  // Check which implementation is available
  var requestMethod = element.requestFullScreen ||
                      element.webkitRequestFullScreen ||
                      element.mozRequestFullScreen ||
                      element.msRequestFullScreen;
  if (requestMethod) {
    requestMethod.apply( element );
    lockPointer();
  }
}

function lockPointer() {
  var element = document.body;
  var requestPointerLock = element.requestPointerLock ||
                           element.mozRequestPointerLock ||
                           element.webkitRequestPointerLock;
  // Ask the browser to lock the pointer
  if (requestPointerLock) {
    requestPointerLock.apply( element );
    setTimeout(handleMouseMoveClick, 3000);
  }
  else {
    alert('Lock Pointer API not found, use latest version of Chrome or Firefox!');
  }
}

var handleMouseMoveClick = function () {
  cursorElement = document.getElementById('cursor');
  maxLeft = window.outerWidth;
  maxTop = window.outerHeight;
  document.addEventListener("mousemove", moveCallback, false);
  document.addEventListener("click", moveClick, false);
}

var lastLeft = 0;
var lastTop = 0;
var nowLeft = 0;
var nowTop = 0;
var maxLeft = 0;
var maxTop = 0;

var lastTarget = null;
var allButtons = null;

function moveCallback (e) {
  var newTarget;
  var movementX = e.movementX ||
      e.mozMovementX          ||
      e.webkitMovementX       ||
      0,
  movementY = e.movementY ||
      e.mozMovementY      ||
      e.webkitMovementY   ||
      0;

  nowLeft = lastLeft + movementX;
  lastLeft = nowLeft < 0 ? 0 : (nowLeft > maxLeft ? maxLeft : nowLeft);

  nowTop = lastTop + movementY;
  lastTop = nowTop < 0 ? 0 : (nowTop > maxTop ? maxTop : nowTop);

  cursorElement.style.left = lastLeft + 'px';
  cursorElement.style.top = lastTop + 'px';

  var coords;
  for (var i = 0, l = buttonsCoordinates.length; i < l; i++) {
    coords = buttonsCoordinates[i];
    if (coords[1] < lastLeft && lastLeft < coords[3] && coords[2] < lastTop && lastTop < coords[4]) {
      if (coords[5] !== lastTarget) {
        newTarget = coords[5].addClass('hover');
      }
      break;
    }
  }

  if (newTarget !== lastTarget) {
    allButtons.not(coords[5]).removeClass('hover');
  }
  else if (newTarget) {
    allButtons.removeClass('hover');
  }

}

function moveClick () {
  var coords;
  for (var i = 0, l = buttonsCoordinates.length; i < l; i++) {
    coords = buttonsCoordinates[i];
    if (coords[1] < lastLeft && lastLeft < coords[3] && coords[2] < lastTop && lastTop < coords[4]) {
      mousePress(document.getElementById(coords[0]).innerText);
      break;
    }
  }
}

function onDocumentKeyDown (event) {
  if (event.keyCode == 70) {
    enterFullscreen();
  }
}

function createButtons () {
  var caption = "";
  var buttons = $("#keyboard");

  for (var x_counter = 0; x_counter <= NUM_END - NUM_BEGIN; x_counter++) {
    // Add a button with the character given by the caption variable and position in based on the loop.
    caption = String.fromCharCode(NUM_BEGIN + x_counter);
    buttons.append(
      new Button(caption, dims.keyboard_x + x_counter*(dims.basic_key_size), dims.keyboard_y, dims.basic_key_size, dims.basic_key_size)
    );
  }

  // Build the letters (one line below numbers)
  for (var y_counter = 0; y_counter < BOARD_HEIGHT; y_counter++) {
    for (var x_counter = 0; x_counter < BOARD_WIDTH; x_counter++) {
      caption = String.fromCharCode(ALPHA_BEGIN + y_counter * BOARD_WIDTH + x_counter);
      // Change the last button for the space
      if (ALPHA_BEGIN + y_counter * BOARD_WIDTH + x_counter == ALPHA_END ) {
        caption = SPACE;
      }

      buttons.append(
        new Button(caption, dims.keyboard_x + (x_counter * (dims.basic_key_size)), dims.keyboard_y + ((y_counter) * dims.basic_key_size) + dims.basic_key_size, dims.basic_key_size, dims.basic_key_size)
      );
    }
  }
  buttons.children().addClass('alphaNum');

  // Special input buttons.
  buttons.append(new Button(CONFIRM, 9*dims.basic_key_size + dims.border_x, dims.keyboard_y + 1*dims.basic_key_size + 0.1 * dims.basic_key_size, dims.wide_key_size, dims.basic_key_size * 0.9));
  buttons.append(new Button(ERASE, 9*dims.basic_key_size + dims.border_x, dims.keyboard_y + 2*dims.basic_key_size + 0.07 * dims.basic_key_size, dims.wide_key_size, dims.basic_key_size * 0.9));
  buttons.append(new Button(KILL, 9*dims.basic_key_size + dims.border_x, dims.keyboard_y + 3*dims.basic_key_size + 0.05 * dims.basic_key_size, dims.wide_key_size, dims.basic_key_size * 0.9));

  // Environment language buttons.
  var font_count = settings.fontCount;
  var button_width = dims.keyboard_width / Math.max(font_count, 1);
  var i = 0;
  for (fontName in settings.font) {
    if (settings.font[fontName]) {
      buttons.append(new Button(fontName, (button_width*i + dims.border_x), dims.border_y, button_width, dims.basic_key_size, settings.font[fontName]));
      buttons.children(':last').addClass('fontButton');
      i++;
    }
  }

  // Output scroll buttons.
  buttons.append(new Button(FIRST, 11*dims.basic_key_size + dims.border_x, dims.output_y + dims.basic_key_size*0, dims.basic_key_size, dims.basic_key_size));
  buttons.append(new Button(PREV, 11*dims.basic_key_size + dims.border_x, dims.output_y + dims.basic_key_size*1, dims.basic_key_size, dims.basic_key_size));
  buttons.append(new Button(NEXT, 11*dims.basic_key_size + dims.border_x, dims.output_y + dims.basic_key_size*2, dims.basic_key_size, dims.basic_key_size));
  buttons.append(new Button(LAST, 11*dims.basic_key_size + dims.border_x, dims.output_y + dims.basic_key_size*3, dims.basic_key_size, dims.basic_key_size));

  handleMouseMoveClick();

  allButtons = $('.button');
}

/**
 * A reaction on a button press. All the logic of virtual buttons is governed here!
 */
var mousePress = function (button) {
  var inputText = data.el.text();
  // Go through letter buttons and space.
  if (/^[a-zA-Z0-9_]{1}$/.test(button)) {
    data.el.text(inputText + button.charAt(0));
  }

  // Action buttons
  else if (button == CONFIRM) {
    data.resetError();

    if (inputText == "") { // Else control for emptyness.
      data.prepend(settings.emptyinput);
    }
    // Else test whether the user does not require to exit.
    else if (inputText.toLowerCase() == "exit") {
      if (settings.illegal) {
        data.prepend(settings.illegallogoff);
      }
      else {
        environment.setScreen(NAME_SCREEN); // ask for name
        data.el.empty();
        data.empty();
        data.prepend(settings.logoffreset);
      }
    }
    else { // Pass the current input to an appropriate handler.
      switch (environment.getScreen()) {
      case NAME_SCREEN:
        confirmName(inputText);
        break;
      case PASS_SCREEN:
        confirmPass(inputText);
        break;
      case TEXT_SCREEN:
        searchText(inputText);
        break;
      }
    }
  } else if (button == ERASE) {
    inputText = inputText.substring(0, inputText.length - 1);
    data.el.text(inputText);
  } else if (button == KILL) {
    data.el.empty();
  }

  else if (button == FIRST) { // Scrollers
    data.scrollFirst();
  } else if (button == PREV) { // Scrollers
    data.scrollBackwards();
  } else if (button == NEXT) { // Scrollers
    data.scrollForward();
  } else if (button == LAST) { // Scrollers
    data.scrollLast();

  } else if (settings.font[button]) { // Font buttons
    environment.setFont(button);
  }
}

/**
 * Called when the user presses confirm button while in the name screen.
 */
function confirmName (input) {
  environment.setAccountName(input);
  environment.setScreen(PASS_SCREEN);
  data.el.empty();
  data.prepend(settings.password.replace('%1$s', input) + "\n");
}

/**
 * Called when the user confirms the typed in password.
 * Control if the user has the access rights - currently take both DENIED and NOT and OK, but sth else should be put here.
 */
function confirmPass (input) {
  environment.password = input;
  data.el.empty();
  http.findEntry("ROLE", function (err, valid) {
    if (err) {
      data.prepend('Chyba spojeni s databazi. Opakujte dotaz znovu.');
      data.setError();
      return;
    }
    if (/^OK/.test(valid.replace('\n',' '))) {
      environment.setScreen(TEXT_SCREEN);
      // Get user name and display prompt
      data.prepend(settings.welcome.replace('%1$s', environment.getAccountName()));
      data.prepend(settings.prompt);
    }
    else {
      environment.setScreen(NAME_SCREEN);
      data.prepend(settings.wronglogin);
    }
  });
}

/**
 * Called when the user confirms the search of the input. The input keyed data are requested from the server and then stored in the output string.
 */
function searchText (input) {
  http.findEntry(input, function (err, res) {
    if (err) {
      data.prepend('Chyba spojeni s databazi. Opakujte dotaz znovu.\n');
      return;
    }
    res = res.replace('\n', ' ');
    if (/^OK/.test(res)) {
      data.el.empty();
      data.prepend(input + ": " + res.substring(3) + '\n');
    }
    else if (/^OFF/.test(res)) {
      data.prepend(input + ": " + settings.off);
    }
    else if (/^DENIED/.test(res)) {
      data.prepend(input + ": " + settings.denied);
    }
    else if (/^NOT/.test(res)) {
      data.prepend(input + ": " + settings.notfound);
    }
    else if (/^CORRUPTED/.test(res)) {
      data.prepend(input + ": " + settings.corrupted);
    }
  });
}

var EnvironmentClass = function (options) {
  if (!options) {
    options = {
      el: $('#environment'),
      screen_type: -1,
      user_name: "",
      password: "",
      on_line: true
    };
  }

  for (i in options) {
    if (Object.prototype.hasOwnProperty.call(options, i)) {
      this[i] = options[i];
    }
  }
  this.el.parent().addClass('w' + settings.screen_width);
  this.start();
};

EnvironmentClass.prototype.start = function () {
  if (settings.illegal) {
    this.setScreen(TEXT_SCREEN);
    data.prepend(settings.illegal_welcome);
  }
  else {
    this.setScreen(NAME_SCREEN);
    data.prepend(settings.username);
  }
};

EnvironmentClass.prototype.setAccountName = function (name) {
  this.user_name = name;
};

EnvironmentClass.prototype.getAccountName = function () {
  return this.user_name;
};

EnvironmentClass.prototype.setPass = function (new_pass) {
  this.password = new_pass;
};

EnvironmentClass.prototype.getPass = function () {
  return this.password;
};

EnvironmentClass.prototype.setScreen = function (new_screen) {
  if (this.screen_type != new_screen) {
    data.el.empty();
    this.screen_type = new_screen;
  }
};

EnvironmentClass.prototype.getScreen = function () {
  return this.screen_type;
};

EnvironmentClass.prototype.setFont = function (fontName) {
  this.el.attr('class', settings.font[fontName]);
};


var AjaxClass = function (options) {
  if (!options) {
    options = {
      url: window.settings.url
    };
  }
  for (i in options) {
    if (Object.prototype.hasOwnProperty.call(options, i)) {
      this[i] = options[i];
    }
  }
  setInterval($.proxy(this.check, this), 10000);
};

AjaxClass.prototype.findEntry = function (keyword, cb) {
  if (this.running) {
    return;
  }

  this.running = true;
  var that = this;

  this.buildQuery(keyword, environment.getAccountName(), environment.getPass(), $.proxy(function(err, res) {
    this.running = false;
  }, this), $.proxy(function (err, res) {
    cb(err, res);
  }, this));
};

AjaxClass.prototype.buildQuery = function (keyword, login, pass, complete, callback) {
  $.ajax({
    url: this.url, type: 'GET', dataType: 'text', timeout: 5000,
    data: {
      'term':     "" + settings.ID + "",
      'klic':     "" + keyword + "",
      'login':    login,
      'password': pass
    },
    error: $.proxy(function () {
      callback(true, null);
    }, this),
    complete: $.proxy(complete, this),
    success: $.proxy(function (responseText) {
      callback(null, responseText);
    }, this)
  });
};

AjaxClass.prototype.afterCheck = function () {
  if (environment.on_line || settings.illegal) {
    $('body.terminal #main').css('backgroundImage', 'url("img/' + (settings.screen_width) + 'x' + (settings.screen_height) + '_1.png")');
  } else {
    $('body.terminal #main').css('backgroundImage', 'url("img/' + (settings.screen_width) + 'x' + (settings.screen_height) + '_off.png")');
  }
};

AjaxClass.prototype.check = function () {
  this.buildQuery("STATUS", "MAINTENANCE", "INSECURITY", function () {
  }, $.proxy(function (err, results) {
    if (err) {
      data.prepend('Chyba spojeni s databazi. Opakujte dotaz znovu.\n');
      data.setError();
    }
    environment.on_line = /ON/.test(results.replace('\n',' '));
    this.afterCheck();
  }, this));
};

DataClass = function (options) {
  if (!options) {
    options = {
      el: $('#inputLine'),
      dt: $('#dta'),
      errorState: false
    };
  }
  for (i in options) {
    if (Object.prototype.hasOwnProperty.call(options, i)) {
      this[i] = options[i];
    }
  }
  this.setupDtaSize();
};
DataClass.prototype.empty = function () {
  this.dt.empty();
  this.el.empty();
  this.scrollFirst();
};
DataClass.prototype.prepend = function (str) {
  this.dt.prepend(document.createTextNode(str));
  this.scrollFirst();
};
DataClass.prototype.resetError = function () {
  if (this.errorState) {
    this.el.removeClass('error');
    this.errorState = false;
  }
};
DataClass.prototype.setError = function () {
  this.el.addClass('error');
  this.errorState = true;
}
DataClass.prototype.setupDtaSize = function () {
  this.dt.css({
    left:   dims.output_x,
    top:    dims.output_y,
    width:  dims.output_width,
    height: dims.output_height
  });
  this.el.css({
    left:   dims.input_x,
    top:    dims.input_y,
    width:  dims.keyboard_width,
    height: dims.text_size * 1.25
  });
  this.maxOver = dims.output_height;

  this.dt = this.dt.find('div').css('marginTop', 0);
};
DataClass.prototype.scrollLast = function () {
  var y = this.maxOver - parseInt(this.dt.height(), 10);
  if (y < 0) {
    this.dt.css('marginTop', y);
  }
};
DataClass.prototype.scrollBackwards = function () {
  this.dt.css('marginTop', Math.min(0, parseInt(this.dt.css('marginTop'), 10)  + 18));
};
DataClass.prototype.scrollForward = function () {
  var y = this.maxOver - parseInt(this.dt.height(), 10);
  if (y < 0) {
    this.dt.css('marginTop', Math.max(y, parseInt(this.dt.css('marginTop'), 10) - 18));
  }
};

DataClass.prototype.scrollFirst = function () {
  this.dt.css('marginTop', 0);
};
