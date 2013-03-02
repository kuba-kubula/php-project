(function(global){

var width_; ///< Width of the window itself.
var height_; ///< Height of the window itself.

// Layout variables - THESE ARE SET IN THE SETTINGS
var border_x; ///< Space between layout and window horizontally.
var border_y; ///< Space between layout and window vertically.
var basic_key_size; ///< Key size.
var caps_size; ///< GUI text.
var text_size; ///< I/O text.
var text_indent = 5;  ///< Space between window and text.

// Object placement variables - just for simplicity in reccurent uses - THESE ARE COMPUTED.
var keyboard_x; ///< Leftmost corner x position of the virtual keyboard.
var keyboard_y; ///< Leftmost corner y position of the virtual keyboard.
var keyboard_width; ///< Leftmost corner y position of the virtual keyboard.
var wide_key_size; ///< Size of the bigger key (e.g. "erase").
var input_x; ///< Leftmost corner x position of the input filed.
var input_y; ///< Leftmost corner y position of the input filed.
var output_x; ///< Leftmost corner x position of the output filed.
var output_y; ///< Leftmost corner y position of the output filed.
var output_width; ///< Width of the output field.
var output_height; ///< Height of the output field.
var data_width; ///< Width of the output text.
var data_height; ///< Height of the output text.
var lines_count; ///< Number of lines in the output filed.

var Settings = function () {
  return this;
}
var is1024 = (location.href.split('?').pop().split('&').pop().split('=').pop() == '1024' ? true : false);
var settings = {
  'on_line': false,
  'screen_width' : is1024 ? 1024 : 800,
  'screen_height' : is1024 ? 768 : 600,
  'url' : "./databaze.php",
  'fontCount' : 4,
  'caps_size': 30,
  'text_size': 20,
  'defaultFont': 'BDI',
  'font' : {
    "Navarenssis"   : "alien_linessymbol",
    "BDI"           : "omikron-webfont",
    "Exarchos"      : "anilloregular",
    "Belliger"      : "silverstreamregular"
  },
  'color': {
    "background"    : "rgba(20,65,20,0.5)",
    "field"         : "rgba(255,255,255,0)",
    "text"          : "rgba(0,255,255,1)",
    "caption"       : "rgba(9,241,187,1)",
    "highlight"     : "rgba(80,160,190,1)",
    "offline"       : "rgba(80,160,190,1)",
    "error"         : "rgba(150,0,0,1)"
  },
  "emptyinput"      : "Vstupni pole je prazdne. Ignoruji.\n",
  "username"        : "Zadejte uzivatelske jmeno.\n",
  "password"        : "Zadejte heslo k uctu %1$s.\n",
  "wronglogin"      : "Neplatne uzivatelske jmeno nebo heslo. Opakujte zadani jmena.\n",
  "welcome"         : "Vitejte uzivateli %1$s.\n",
  "illegal_welcome" : "Vitejte uzivateli ... CHYBA! UZIVATELSKY UCET NEPLATNY, NEOPRAVNENY PRISTUP JE TRESTNY!\n",
  "prompt"          : "Pro odhlaseni zadejte EXIT a potvrdte. Pro vyhledavani zadejte heslo a potvrdte.\n",
  "outofbounds"     : "Prekrocena delka vstupniho pole.\n",
  "off"             : "Spojeni s databazi je v tuto chvili vypnute.\n",
  "denied"          : "K zobrazeni tohoto hesla nemate dostatecne pravomoce.\n",
  "notfound"        : "Heslo nebylo nalezeno v databazi.\n",
  "corrupted"       : "Heslo je v tuto chvili nedostupne.\n",
  "logoffreset"     : "Uzivatel odhlasen. Zadejte uzivatelske jmeno.\n",
  "illegal_logoff"  : "CHYBA ODPOJENI. NEOPRAVNENE UZIVANI TERMINALU JE TRESTNE!\n",
  "engineer"        : "Servisni pristup povolen. Pouzijte jeden z techto 3 prikazu: STATUS - OFF - ON\n"
}

  width_ = settings.screen_width;
  height_ = settings.screen_height;

  // Relate basic values to this setting
var dims = {};

dims.width_ = settings.screen_width;
dims.height_ = settings.screen_height;
dims.border_y = Math.round(height_ * 0.05);
dims.border_x = Math.round(height_ * 0.05) + Math.round((width_ - height_*1.3)/2.0);
dims.basic_key_size = Math.round(height_ *0.1);
dims.caps_size = settings.caps_size;
dims.text_size = settings.text_size;
dims.text_indent = 5;

// Compute helping values from the basic ones.
dims.keyboard_x     = dims.border_x;
dims.keyboard_y     = dims.height_ - dims.basic_key_size * 4 - dims.border_y;
dims.keyboard_width = dims.basic_key_size * 12;
dims.wide_key_size  = dims.basic_key_size * 3;
dims.input_x        = dims.border_x;
dims.input_y        = Math.round(dims.basic_key_size * 1.25) + dims.border_y;
dims.output_x       = dims.border_x;
dims.output_y       = Math.round(dims.basic_key_size * 1.75) + dims.border_y;
dims.output_width   = dims.keyboard_width - dims.basic_key_size;
dims.output_height  = dims.basic_key_size * 3;
dims.data_width     = dims.output_width - 2 * dims.text_indent;
dims.data_height    = dims.output_height - 2 * dims.text_indent;
dims.lines_count    = dims.data_height / dims.text_size // This is tweaked a bit to make sure no overlap occurs in the bottom.

settings.illegal = location.href.indexOf('id=0') === -1 ? false : true;
settings.ID = location.href.split('?').pop().split('&').shift().replace('id=', '');

global.settings = settings;
global.dims = dims;

})(window);
