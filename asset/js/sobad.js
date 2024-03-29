// support jQuery Core
const server = hosting + system;
//const socket = io(websocket);

var url_ajax = "include/ajax.php";
const url_preview = "include/preview.php";
const url_sending = "include/sending.php";
const url_notif = "include/notify.php";
var filter = "";
var uploads = "";
var repeater = "";

var modal_toggle = false;
var index_toggle = "";

var d = new Date();
d.setTime(d.getTime() + 10 * 60 * 60 * 1000); // 10 jam

var expire = d.toUTCString();

var data;

//click button
$(document).ready(function () {
  // chart onload
  $(".chart_malika").ready(function () {
    if ($("div").hasClass("chart_malika")) {
      var ajx = $(".chart_malika").attr("data-sobad");
      var id = $(".chart_malika").attr("data-load");

      data = "ajax=" + ajx + "&data=2019";
      //sobad_ajax(id,data,sobad_chart);
    }
  });

  // click button sidemenu
  $(".sobad_sidemenu").click(function () {
    var ajx = $(this).attr("id");
    if (ajx != "sobad_#" && ajx != "sobad_") {
      $("ul.page-sidebar-menu li").removeClass("start open active");
      $(this).parent().addClass("start open active");
      $(this).parent().parent().parent().addClass("start open active");
      sobad_sidemenu(this);
    }
  });

  // click button logout
  $(".sobad_logout").click(function () {
    sobad_load_togle("#myModal");
    setTimeout(function () {
      data = "ajax=sobad__get&object=logout_system&data=1";
      sobad_ajax("Logout Berhasil", data, sobad_direct, true, "", "");
    }, 1000);
  });
});

$(window).on("popstate", function () {
  sobad_load("here_content");

  var object = location.pathname;
  object = object.replace("/" + system + "/", "");
  sobad_history(object);
});

var audio = new Audio(server + '/asset/audio/notif.mp3');

dbRef.on("child_changed", function(data) {
	sobad_ajax_notification(data.val());
});

function sobad_ajax_notification(data){
  $.ajax({
    url: server + "/" + url_notif,
    type: "POST",
    data: "data=" + data,
    success: function (response) {
      result = JSON.parse(response);

        if(result['notify']){
          audio.play();
          toastr.options = {
              progressBar: true,
              closeButton: true,
              iconClasses: {
                  info: result['icon'],
              }
        };

        toastr.info(result['msg'], result['title'], {timeOut: 15000})
      }

      sobad_notification(result);
    },
    error: function (jqXHR) {
      if (jqXHR.status == 0) {
        alert(" fail to connect, please check your connection settings");
      }
    },
  });
}

function sobad_notification(data){
	// Bell Notification
	var bell_notify = data['bell_notify'];

	if(bell_notify['status']){
		$('#bell-notify').removeClass('hide');
	}else{
		$('#bell-notify').addClass('hide');
	}

	if(bell_notify['qty'] < 1){
		$('#tabs-notify').addClass('hide');
	}else{
		$('#tabs-notify').removeClass('hide');
	}

	$('#tabs-notify').html('<small>' + bell_notify['qty'] + '</small>');
	$('#list-notify').html(bell_notify['data']);

	// Menu Notification
	sasi_dataMenu = data['menu_notify'];
	sobad_menu_notif(sasi_dataMenu);
}

function sobad_menu_notif(obj){
	for (const [key, value] of Object.entries(obj)) {
		if(value['child'] != null){
			sobad_menu_notif(value['child']);
		}

		if(value['notify'] < 1){
			$('span[id=' + value['id'] + ']').addClass('hide');
		}else{
			$('span[id=' + value['id'] + ']').removeClass('hide');
		}

		$('span[id=' + value['id'] + ']').html('<small>' + value['notify'] + '</small>');
	}
}

function setcookie(key, data) {
  document.cookie = key + "=" + data + ";expires=" + expire + ";path=/";
}

// function sidemenu
function sobad_sidemenu(val) {
  var ajx = $(val).attr("id");
  if (ajx != "sobad_#" && ajx != "sobad_") {
    sobad_load("here_content");

    var uri = $(val).attr("data-uri");
    //setcookie("sidemenu",ajx);
    window.history.pushState(sobad_history(uri), ajx, "/" + system + "/" + uri);
  }
}

// function sidemenu
function sobad_newpage(val) {
  var ajx = $(val).attr("id");
  sobad_load("here_content");

  var uri = $(val).attr("data-uri");
  //setcookie("sidemenu",ajx);
  window.history.pushState(
    sobad_history_page(val),
      ajx,
      "/" + system + "/" + object + "/" + uri
    );
}

// function history sidemenu
function sobad_history(obj) {
  obj = obj.replace("sobad_", "");
  object = obj;

  data = "ajax=_sidemenu&object=" + obj + "&data=";
  sobad_ajax("#here_content", data, "html", false);
}

// function history newpage
function sobad_history_page(val) {
  var ajx = $(val).attr("data-sobad");
  var uri = $(val).attr("data-uri");
  var tp = $(val).attr("data-type");

  data = "ajax=" + ajx + "&object=" + object + "&data=" + uri + "&type=" + tp;
  sobad_ajax("#here_content", data, "html", false);
}

// function tabs
function sobad_tabs(val) {
  sobad_load("tab_malika");
  sobad_button(val, false);
}

// function tabs
function sobad_options(val) {
  var ajx = $(val).attr("data-sobad");
  if (ajx) {
    var lbl = val.value;
    var id = $(val).attr("data-load");
    var att = $(val).attr("data-attribute");

    data = "ajax=" + ajx + "&object=" + object + "&data=" + lbl;
    sobad_ajax("#" + id, data, att, false);
  }
}

function sobad_live_search(val) {
  var tag = $(val).parent().parent().parent().parent().children("select");
  var dt = tag.attr("data-change");
  if (dt) {
    sobad_loading(".bs-select ul.selectpicker");

    data = "ajax=" + dt + "&object=" + object + "&data=" + $(val).val();
    sobad_ajax(tag, data, sobad_select_liveSearch, false);
  }
}

function sobad_select_liveSearch(data, id) {
  $(id).html(data);
  $(id).selectpicker("refresh");

  $("div.bs-select:nth-child(2) ul.selectpicker .blockUI").remove();
}

function sobad_option_search(data, id) {
  $(id).html(data);
  $(id + ".bs-select").selectpicker("refresh");
}

function sobad_selectOption_search(data, id) {
  sobad_option_search(data, id);

  $("div.bs-select:nth-child(2) ul.selectpicker .blockUI").remove();
}

// function button
function sobad_button(val, spin) {
  var alert = $(val).attr("data-alertbefore");

  if(alert){
      var sw_title = $(val).attr("data-alerttitle");
      var sw_icon = $(val).attr("data-alerticon");
      var sw_type = $(val).attr("data-alerttype");
      var sw_btn = $(val).attr("data-alertbutton");

      swal({
        title: sw_title,
        text: '',
        icon: sw_icon,
        type: sw_type,
        buttons: ['Cancel', sw_btn],
    }).then((willSubmit) => {
        if (!willSubmit) {
            return false;
        }else{
            sobad_button_execution(val,spin);
        }
    });
  }else{
      sobad_button_execution(val,spin);
  }
}

function sobad_button_execution(val,spin){
  var id = $(val).attr("data-load");

  var ajx = $(val).attr("data-sobad");
  var lbl = $(val).attr("id");
  var msg = $(val).attr("data-alert");
  var tp = $(val).attr("data-type");
  var notif = $(val).attr("data-notify");
  var _object = $(val).attr("object");

  // Object Tambahan
  var object_func = _object || object;

  var pg = $("#dash_pagination li.disabled a").attr("data-qty");
  var data = $("form").serializeArray();
  data = conv_array_submit(data);

  sobad_load_togle($(val).attr("href"));

  // loading
  var html = $(val).html();
  if (spin) {
    $(val).html('<i class="fa fa-spinner fa-spin"></i>');
    $(val).attr("disabled", "");
  }

  data =
    "ajax=" +
    ajx +
    "&object=" +
    object_func +
    "&data=" +
    lbl +
    "&args=" +
    data +
    "&type=" +
    tp +
    "&page=" +
    pg +
    "&filter=" +
    filter +
    "&notify=" +
    notif;
  sobad_ajax("#" + id, data, "html", msg, val, html);
}

// click button import
function sobad_import(val) {
  var idx = $("#importFile").attr("data-load");

  $.ajax({
    url: server + "/" + url_ajax, // Url to which the request is send
    type: "POST", // Type of request to be send, called as method
    data: new FormData(val), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false, // To send DOMDocument or non processed data file it is set to false
    success: function (
      response // A function to be called if request succeeds
    ) {
      var req = sobad_callback("#" + idx, response, "html", true);
    },
  });
}

function sobad_button_pre(val) {
  var id = $(val).attr("id");
  var pre = $(val).attr("data-sobad");
  var data = "page=" + pre + "&object=" + object + "&data=" + id;

  sobad_load_togle($(val).attr("href"));

  sobad_preview(url_preview, data, "", "", "");
}

function sobad_report(val) {
  var pre = $(val).attr("data-sobad");
  var tp = $(val).attr("data-type");
  var data = $("form").serializeArray();
  data = conv_array_submit(data);
  data =
    "page=" +
    pre +
    "&object=" +
    object +
    "&data=" +
    data +
    "&type=" +
    tp +
    "&filter=" +
    filter;

  sobad_preview(url_preview, data, "");
}

function sobad_submitLoad(val) {
  sobad_load_submit(val, true);
}

function sobad_submit(val) {
  sobad_load_submit(val, false);
}

// click button submit
function sobad_load_submit(val, spin) {
  var ajx = $(val).attr("data-sobad");
  var id = $(val).attr("data-load");
  var tp = $(val).attr("data-type");
  var index = $(val).attr("data-index");
  var mdl = $(val).attr("data-modal");
  var node = $(val).attr("data-socket");
  var _object = $(val).attr("object");
  var notif = $(val).attr("data-notify");

  // Object tambahan
  var object_func = _object || object;

  var callback = "sobad_option_search";
  if (node) {
    callback = node;
  }

  var srcData = $("form.sobad_form").serializeArray();
  var data = $("form" + index).serializeArray();

  data = data.concat(srcData);
  if ($("#summernote_1").length > 0) {
    var note = sobad_get_summernote();

    data[4]["value"] = "";
    data = data.concat(note);
  }

  if ($("#cke_editor_text").length > 0) {
    var editor = sobad_get_ckeditor();

    data[4]["value"] = "";
    data = data.concat(editor);
  }

  if ($("input[type=file]").length > 0) {
    var fileInput = sobad_get_fileInput();
    data = data.concat(fileInput);
  }

  if (mdl == "1") {
    modal_toggle = true;
    index_toggle = val;
  }

  // loading
  var html = $(val).html();
  if (spin) {
    $(val).html('<i class="fa fa-spinner fa-spin"></i>');
    $(val).attr("disabled", "");
  }

  var pg = $("#" + id + " #dash_pagination li.disabled a").attr("data-qty");
  data = conv_array_submit(data);

  data =
    "ajax=" +
    ajx +
    "&object=" +
    object_func +
    "&data=" +
    data +
    "&type=" +
    tp +
    "&page=" +
    pg +
    "&filter=" +
    filter +
    "&repeater=" +
    repeater +
    "&notify=" +
    notif;
  sobad_ajax("#" + id, data, callback, true, val, html);
}

// get data summernote
function sobad_get_summernote() {
  var data = $("#summernote_1").code();
  var name = $("#summernote_1").attr("name");

  args = [
    {
      name: name,
      value: ascii_to_hexa(data),
    },
  ];

  return args;
}

// get data summernote
function sobad_get_ckeditor() {
  var id;
  for (var instanceName in CKEDITOR.instances) {
    id = instanceName;
  }

  var data = CKEDITOR["instances"][id].getData();
  var name = "ckeditor";

  args = [
    {
      name: name,
      value: ascii_to_hexa(data),
    },
  ];

  return args;
}

// get data summernote
function sobad_get_fileInput() {
  var formData = new FormData();
  var file = $("input[type=file]")[0].files;

  if (file.length > 0) {
    formData.append("ajax", "sendMail_fileUpload");
    formData.append("object", object);
    for (var i = 0; i < file.length; i++) {
      formData.append("file[]", file[i]);
    }

    sobad_upload(formData); // data di uploads
  }

  args = [
    {
      name: "attachment",
      value: uploads,
    },
  ];

  return args;
}

// click button search
function sobad_search(val) {
  var ajx = $(val).attr("data-sobad");
  var data = $(".sobad_form").serializeArray();
  var load = $(val).attr("data-load");
  //var obj = $(val).attr('data-object');
  var tp = $(val).attr("data-type");
  var pg = $("#dash_pagination li.disabled a").attr("data-qty");
  data = conv_array_submit(data);

  sobad_load(load);

  data =
    "ajax=" +
    ajx +
    "&object=" +
    object +
    "&data=" +
    data +
    "&type=" +
    tp +
    "&page=" +
    pg +
    "&filter=" +
    filter;
  sobad_ajax("#" + load, data, "html", false, "", "");
}

// click pagination
function sobad_pagination(val) {
  var ajx = $(val).attr("data-sobad");
  var args = $(".sobad_form").serializeArray();
  var data = $(val).attr("data-qty");
  var load = $(val).attr("data-load");
  //var obj = $(val).attr('data-object');
  var tp = $(val).attr("data-type");
  args = conv_array_submit(args);

  sobad_load(load);

  data =
    "ajax=" +
    ajx +
    "&object=" +
    object +
    "&data=" +
    data +
    "&type=" +
    tp +
    "&args=" +
    args +
    "&filter=" +
    filter;
  sobad_ajax("#" + load, data, "html", false, "", "");
}

// click filter
function sobad_filtering(val) {
  var id = $(val).attr("data-load");
  var ajx = $(val).attr("data-sobad");
  var tp = $(val).attr("data-type");
  var dt = $(val).val();

  sobad_load(id);

  filter = dt;
  var data =
    "ajax=" + ajx + "&object=" + object + "&data=" + dt + "&type=" + tp;
  sobad_ajax("#" + id, data, "html", false, "", "");
}

function sobad_load(id) {
  sobad_loading("#" + id);
}

function sobad_loading(id) {
  var html = (html =
    '<div class="loading-message">' +
    '<div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>' +
    "</div>");
  var cenrerY = false;

  var el = $(id);
  if (el.height() <= $(window).height()) {
    cenrerY = true;
  }

  el.block({
    message: html,
    baseZ: 1000,
    centerY: cenrerY,
    css: {
      top: "10%",
      border: "0",
      padding: "0",
      backgroundColor: "none",
    },
    overlayCSS: {
      backgroundColor: "#555",
      opacity: 0.1,
      cursor: "wait",
    },
  });
}

function sobad_load_togle(id = "") {
  var load =
    '<div class="modal-content"><div class="modal-body"><img src="asset/img/loading-spinner-grey.gif" alt="" class="loading"><span> &nbsp;&nbsp;Loading... </span></div></div>';

  if (id.indexOf(":") == -1 && id.indexOf(";") == -1 && id.indexOf(",") == -1) {
    if (id == "#myModal") {
      $("#myModal #here_modal").html(load);
    } else if (id == "#myModal2") {
      $("#myModal2 #here_modal2").html(load);
    } else if (id == "#myModal3") {
      $("#myModal3 #here_modal3").html(load);
    } else {
      $("#myModal" + id + " #here_modal" + id).html(load);
    }
  }
}

function sobad_set_upload(msg, id) {
  uploads = msg;
}

function sobad_direct(to, msg) {
  if (msg != "") {
    toastr.success(msg);
  }
  document.location.href = to;
}

function sobad_windows(idx) {
  idx = idx.replace("sobad_", "");
  window.open(server + "/" + idx);
}

function sobad_preview(url, data, spec) {
  if (spec == "") {
    spec = "top=200,left=350,width=800,height=800";
  }

  //  data = $.rot13(data);
  url = server + "/" + url_preview + "?" + data;
  window.open(url, "scrollwindow", spec);
}

function sobad_picker() {
  if (jQuery().datepicker) {
    $(".date-picker").datepicker({
      rtl: false,
      orientation: "right",
      autoclose: true,
    });
  }
}

function sobad_clockpicker() {
  if (jQuery().clockpicker) {
    $(".clockpicker").clockpicker({
      placement: "right",
      align: "left",
      autoclose: true,
    });
  }
}

function sobad_notify(msg){
  dbRef.update({notif:msg});
}

function conv_array_submit(arr) {
  for (var ky in arr) {
    arr[ky]["value"] = ascii_to_hexa(arr[ky]["value"].replace(/\+/g, "-plus-"));
  }

  return JSON.stringify(arr);
}

function ascii_to_hexa(str) {
  str = str.replace(/\+/g, "-plus-");
  str = encodeURI(str);

  var arr1 = [];
  for (var n = 0, l = str.length; n < l; n++) {
    var hex = Number(str.charCodeAt(n)).toString(16);
    arr1.push(hex);
  }
  return arr1.join("");
}

function mask_money(val) {
  $(val).maskMoney({
    allowNegative: true,
    thousands: ".",
    decimal: ",",
    allowZero: true,
    precision: 0,
    allowEmpty: true,
  });
}

function mask_decimal(val) {
  $(val).maskMoney({
    allowNegative: true,
    thousands: ".",
    decimal: ",",
    allowZero: true,
    precision: 1,
    allowEmpty: true,
  });
}

function mask_quantity(val) {
  $(val).maskMoney({
    allowNegative: true,
    thousands: ".",
    decimal: ",",
    allowZero: true,
    precision: 2,
  });
}

function mask_decimal3(val) {
  $(val).maskMoney({
    allowNegative: true,
    thousands: ".",
    decimal: ",",
    allowZero: true,
    precision: 3,
  });
}

function mask_decimalDigit(val,digit) {
  $(val).maskMoney({
    allowNegative: true,
    thousands: ".",
    decimal: ",",
    allowZero: true,
    precision: digit,
  });
}

function number_format(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function remove_format(x) {
  x = x.replace(/\./g, "");
  return parseInt(x, 10);
}

function prefix_format(num, digits) {
  var si = [
    { value: 1, symbol: "" },
    { value: 1e3, symbol: "K" },
    { value: 1e6, symbol: "M" },
    { value: 1e9, symbol: "G" },
    { value: 1e12, symbol: "T" },
    { value: 1e15, symbol: "P" },
    { value: 1e18, symbol: "E" },
  ];
  var rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
  var i;
  for (i = si.length - 1; i > 0; i--) {
    if (num >= si[i].value) {
      break;
    }
  }
  return (num / si[i].value).toFixed(digits).replace(rx, "$1") + si[i].symbol;
}

function sobad_upload(data) {
  $.ajax({
    url: server + "/" + url_ajax,
    type: "POST",
    data: data,
    async: false,
    contentType: false, // The content type used when sending data to the server.
    cache: false, // To unable request pages to be cached
    processData: false, // To send DOMDocument or non processed data file it is set to false
    success: function (response) {
      var req = sobad_callback("", response, sobad_set_upload);
    },
  });
}

function sobad_ajax(id, data, func, msg, val, html) {
	var queryString = window.location.search;

  $.ajax({
    url: server + "/" + url_ajax + queryString,
    type: "POST",
    data: data,
    success: function (response) {
      var req = sobad_callback(id, response, func, msg);
    },
    error: function (jqXHR) {
      if (jqXHR.status == 0) {
        alert(" fail to connect, please check your connection settings");
      }
    },
    complete: function () {
      if (val) {
        $(val).removeAttr("disabled");
        $(val).html(html);
      }
    },
  });
}

function sobad_callback(id, response, func, msg) {
  result = JSON.parse(response);

  switch (result["status"]) {
    case "failed":
      toastr.error(result["msg"]);
      break;
    case "error":
      toastr.error(result["msg"]);
      break;
    case "success":
      if (msg) {
        toastr.success(result["msg"]);
      }

      if (result['notify'] && notify_status) {
        sobad_notify(result['id_notify']);
      }

      if (modal_toggle) {
        $(index_toggle).parent().parent().parent().parent().modal("hide");
        var mdl = $(index_toggle).parent().parent().parent().attr("id");
        $("#" + mdl).html("");

        modal_toggle = false;
        index_toggle = "";
      }

      if (typeof func == "function") {
        func(result["data"], id);
      } else if (typeof window[func] == "function") {
        window[func](result["data"], id);
      } else {
        if (typeof $(id)[func] == "function") {
          func = "inner" in result ? result["inner"] : func;
          $(id)[func](result["data"]);
        }
      }
      break;
    default:
      break;
  }
}

if(notify_status){
	sobad_ajax_notification('#');
}