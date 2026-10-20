$(function () {
  function bukaSidebar() {
    $("#sidebar").addClass("is-open");
    $("#sidebar-backdrop").fadeIn(200);
  }

  function tutupSidebar() {
    $("#sidebar").removeClass("is-open");
    $("#sidebar-backdrop").fadeOut(200);
  }

  $("#menu-toggle").on("click", bukaSidebar);
  $("#sidebar-close, #sidebar-backdrop").on("click", tutupSidebar);

  $(".nav-link").on("click", function () {
    var target = $(this).data("target");

    if (!$(this).hasClass("is-active")) {
      $(".nav-link").removeClass("is-active").removeAttr("aria-current");
      $(this).addClass("is-active").attr("aria-current", "page");

      $(".page-section").hide();
      $(target).fadeIn(300);
    }

    tutupSidebar();
  });

  var $baris = $("#tabel-pesan tbody tr");

  function nomorUlang() {
    var nomor = 0;
    $baris.not(".d-none").each(function () {
      nomor++;
      $(this).find(".col-no").text(nomor);
    });
  }

  $("#search").on("input", function () {
    var kata = $.trim($(this).val()).toLowerCase();
    var tampil = 0;

    $baris.each(function () {
      var $tr = $(this);
      var teks = [
        $tr.attr("data-nama"),
        $tr.attr("data-hp"),
        $tr.attr("data-alamat"),
        $tr.attr("data-pesan")
      ].join("\u0001").toLowerCase();

      var cocok = kata === "" || teks.indexOf(kata) !== -1;
      $tr.toggleClass("d-none", !cocok);
      if (cocok) {
        tampil++;
      }
    });

    nomorUlang();

    if ($baris.length > 0) {
      $("#tabel-wrapper").toggle(tampil > 0).scrollTop(0);
      $("#no-result").toggle(tampil === 0);
    }
  });

  function bukaPreview($tr) {
    $("#pv-nama").text($tr.attr("data-nama"));
    $("#pv-hp").text($tr.attr("data-hp"));
    $("#pv-alamat").text($tr.attr("data-alamat"));
    $("#pv-pesan").text($tr.attr("data-pesan"));
    $("#modal-preview").modal("show");
  }

  $("#tabel-pesan tbody")
    .on("click", "tr", function (event) {
      if ($(event.target).closest(".btn-hapus").length) {
        return;
      }
      bukaPreview($(this));
    })
    .on("keydown", "tr", function (event) {
      if (event.key === "Enter" && event.target === this) {
        bukaPreview($(this));
      }
    });

  $("#tabel-pesan").on("click", ".btn-hapus", function () {
    var $tr = $(this).closest("tr");
    $("#hapus-id").val($tr.attr("data-id"));
    $("#hapus-nama").text($tr.attr("data-nama"));
    $("#modal-hapus").modal("show");
  });

  var POLA_HP = /^(\+62|62|0)[0-9]{8,13}$/;

  $(".form-field input, .form-field textarea").on("input", function () {
    $(this).removeClass("is-invalid");
  });

  $("#form-pesan").on("submit", function (event) {
    var errors = [];
    var $pertama = null;

    function tandai(selector, pesan) {
      $(selector).addClass("is-invalid");
      errors.push(pesan);
      if (!$pertama) {
        $pertama = $(selector);
      }
    }

    $(".is-invalid").removeClass("is-invalid");

    var nama = $.trim($("#f-nama").val());
    var noHp = $.trim($("#f-hp").val()).replace(/[\s\-]/g, "");
    var alamat = $.trim($("#f-alamat").val());
    var pesan = $.trim($("#f-pesan").val());

    if (nama === "") {
      tandai("#f-nama", "Nama wajib diisi.");
    }

    if (noHp === "") {
      tandai("#f-hp", "No. HP wajib diisi.");
    } else if (!POLA_HP.test(noHp)) {
      tandai("#f-hp", "Format No. HP tidak valid (contoh: 081234567890).");
    }

    if (alamat === "") {
      tandai("#f-alamat", "Alamat wajib diisi.");
    }

    if (pesan === "") {
      tandai("#f-pesan", "Pesan wajib diisi.");
    }

    if (errors.length > 0) {
      event.preventDefault();
      $("#form-error").text(errors.join(" ")).hide().slideDown(200);
      $pertama.trigger("focus");
      return;
    }

    $("#form-error").hide();
    $("#btn-kirim").prop("disabled", true).text("Mengirim...");
  });

  $(window).on("pageshow", function () {
    $("#btn-kirim").prop("disabled", false).text("Kirim Pesan");
  });

  $("#modal-pesan")
    .on("shown.bs.modal", function () {
      $("#f-nama").trigger("focus");
    })
    .on("hidden.bs.modal", function () {
      $("#form-pesan")[0].reset();
      $("#form-error").hide();
      $(".is-invalid").removeClass("is-invalid");
    });

  $("#notifikasi .alert").each(function () {
    var lama = $(this).hasClass("alert-danger") ? 7000 : 4500;
    $(this)
      .delay(lama)
      .fadeOut(400, function () {
        $(this).remove();
      });
  });

  if (window.history.replaceState && window.location.search !== "") {
    window.history.replaceState(null, "", window.location.pathname);
  }
});