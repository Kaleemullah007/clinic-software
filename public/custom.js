//  --------select2 js ---------
$( document ).ready(function() {
    $(".select2").select2();
});

setTimeout(() => {
    $( document ).ready(function() {

        $(function() {
            $('input[name="daterange"]').daterangepicker({
              opens: 'left'
            }, function(start, end, label) {
              console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
          });
    });


    // $(".getSales").on("change", function () {
    //     getSales();
    // });

}, 1000);
// daterangepicker



// //  --------sidebar active transition js ---------
// $(document).on('click', '#sidebar li', function() {
//     $(this).addClass('active').siblings().removeClass('active')
// });

// //  --------sidebar collapse toggle js ---------
// $(document).ready(function() {
//     $("#toggleSidebar").click(function() {
//         $(".left-menu").toggleClass("hide");
//         $(".content-wrapper").toggleClass("hide");
//     });
// });

// --------- left menu sidebar dropdown toggle    ---------
$('.sub-menu ul').hide();
$('.sub-menu a').click(function() {
    $(this).parent(".sub-menu").children("ul").slideToggle("180");
    $(this).find(".right").toggleClass("bi-caret-up-fill bi-caret-down-fill");
});

// ------- datatables js ------
$(document).ready(function () {
    $('#order-table').DataTable();
});

// ------- full screen button ---------
const toggleFullScreen = () => {
    let doc = window.document;
    let docEl = doc.documentElement;

    let requestFullScreen =
      docEl.requestFullscreen ||
      docEl.mozRequestFullScreen ||
      docEl.webkitRequestFullScreen ||
      docEl.msRequestFullscreen;
    let cancelFullScreen =
      doc.exitFullscreen ||
      doc.mozCancelFullScreen ||
      doc.webkitExitFullscreen ||
      doc.msExitFullscreen;

    if (
      !doc.fullscreenElement &&
      !doc.mozFullScreenElement &&
      !doc.webkitFullscreenElement &&
      !doc.msFullscreenElement
    ) {
      requestFullScreen.call(docEl);
    } else {
      cancelFullScreen.call(doc);
    }
  };


allproducts = [];
function getPrice(id) {
    product_id = id + "-service_id";
    selected_product = $("#" + product_id).val();
    var productid = $("#" + id + "-service_id").val();
    if (productid == "Choose") {
        alert("Select Product to add new");
        return false;
    }

    // if ($.inArray(productid, allproducts) >= 0) {
    //     var div = $("#setting-row" + id + " > div:parent");
    //     var x = $("#setting-row" + id)
    //         .prev()
    //         .attr("id");

    //     var backway = parseInt(x.split("").reverse().join(""));

    //     $("input[name^='service_id']").each(function (index, val) {
    //         var id_loop = this.id;
    //         var product_input_id = parseInt(id_loop);

    //         product_id = product_input_id + "-service_id";
    //         lop_selected_product = $("#" + product_id).val();

    //         // console.log(lop_selected_product+' '+selected_product)
    //         if (selected_product == lop_selected_product) {
    //             if (id_loop.includes("qty")) {
    //                 var row_id = parseInt(id_loop);
    //                 product_id = $("#" + row_id + "-service_id").val();

    //                 product_qty = product_input_id + "-qty";
    //                 old_value = parseInt($("#" + product_qty).val()) + 1;
    //                 $("#" + product_qty).val(old_value);
    //                 $("#" + id + "-service_id").val("");
    //                 $("#" + id + "-service_id" + " option")
    //                     .filter(function () {
    //                         //may want to use $.trim in here
    //                         return $(this).text() == "Choose";
    //                     })
    //                     .prop("selected", true);

    //                 $.ajax({
    //                     type: "GET",
    //                     url: "/get-price/" + productid,
    //                     success: function (data) {
    //                         // $("#"+id+"-sale_price").val(data.sale_price)

    //                         // $("#"+id+"-available-stock").css({"font-size":"12px","color":data.color,"font-weight":"bold"}).text(" Available("+data.stock+")");

    //                         calcualtePrice();
    //                     },
    //                 });
    //             }
    //         }
    //     });

    //     // return false;
    // } else {
    //     $("input[name^='service_id']").each(function (index, val) {
    //         var id = this.id;
    //         var row_id = parseInt(id);
    //         product_id = $("#" + row_id + "-service_id").val();
    //         allproducts.push(product_id);
    //     });

        $.ajax({
            type: "GET",
            url: "/get-price/" + productid,
            success: function (data) {
                $("#" + id + "-price").val(data.price);
                calcualtePrice();
            },
        });
    // }

    // $("input[name^='products']").each(function (index,val) {
    //     var id = this.id;
    //     if(id.includes('qty')){
    //         var row_id = parseInt(id);
    //         product_id =  $("#"+row_id+"-product_id").val();
    //         allproducts.push(product_id);
    //     }
    //  });
}

function calcualtePrice() {
    var paid_amount = $("#paid_amount").val();
    var discount = $("#discount").val();
    if(discount.length==0){
        discount = 0;
    }

    if(paid_amount.length==0){
        paid_amount = 0;
    }
    var n = $("select[name^='service_id']").length;
    var price = 0;
    var qty = 0;
    $("select[name^='service_id']").each(function (index, val) {
        var id = this.id;

        var id_loop = id;
        var product_input_id = parseInt(id_loop);

        product_id = product_input_id + "-service_id";
        lop_selected_product = $("#" + product_id).val();

        if (lop_selected_product != "Choose") {

                var row_id = parseInt(id);

                row_price = parseFloat($("#" + row_id + "-price").val());
                price += row_price;

        }
    });
    // var values = $("input[name='products[]']")
    //       .map(function(){return $(this).val();}).get();


    sale_price = price;

    var total_amount = parseFloat(sale_price) - parseFloat(discount);
    var subtotal = parseFloat(sale_price);
    var remaining_amount = total_amount - parseFloat(paid_amount);
    $("#remaining_amount").val(remaining_amount);
    $("#total").val(total_amount);
    $("#remaining").text(remaining_amount);
    $("#paid").text(paid_amount);
    $("#sub_total").text(subtotal);
    $("#show_discount").text(discount);
    $("#show_total").text(total_amount);
    // $("#paid_amount").val(subtotal)
}

$(document).on("change", ".calculation, #paid_amount, #discount", function () {
    calcualtePrice();
});


$(document).on("change", "#same_whatsapp", function () {

    var cc = $("#same_whatsapp").is(":checked");
    if(cc == true){
        $("#same_whatsapp_").val(1);
        phone = $("#phone").val();
        $("#whatsapp_number").val(phone);

    }else{
        $("#same_whatsapp_").val(0);
        $("#whatsapp_number").val('');
    }

});



$(document).on("change", "#same_whatsapp", function () {

    var cc = $("#same_whatsapp").is(":checked");
    if(cc == true){
        $("#same_whatsapp_").val(1);
        phone = $("#phone").val();
        $("#whatsapp_number").val(phone);

    }else{
        $("#same_whatsapp_").val(0);
        $("#whatsapp_number").val('');
    }

});


$(document).on("change", "#is_paid_", function () {

    var cc = $("#is_paid_").is(":checked");
    if(cc == true){
        $("#is_paid").val(1);
        $("#payments_").show();



    }else{
        $("#is_paid").val(0);
        $("#payments_").hide();

    }

});

function addSetting(id) {
    // 0-qty
    allproducts = [];
    $("input[name^='service_id']").each(function (index, val) {
        var id = this.id;
        // console.log(this.value+' '+id)

        var row_id = parseInt(id);
        product_id = $("#" + row_id + "-service_id").val();
        allproducts.push(product_id);
    });

    $("#setting-row" + id + "-href").attr("disabled", true);
    var OldRow = id;

    totalrows = $(".setting > .setting-row").length;
    // alert(totalrows);
    totalrecord = $(".totalrecord-settings").length;
    var div = $(".setting > .setting-row:last");
    FirstRowId = div.attr("id");
    lastRow = FirstRowId.split("setting-row");
    //  console.log(lastRow);
    product_id = id + "-service_id";
    selected_product = $("#" + product_id).val();

    if (selected_product == "Choose") {
        alert("Select Service to add new");
        return false;
    }

    var NextRow = parseInt(lastRow[1]) + 1;

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    var addButton =
        '<a href="javascript:void(0)" class="btn btn-success " onclick="addSetting(' +
        NextRow +
        ')"><i class="bi bi-plus-lg"></i> Add</a>';
    var removeButton =
        '<a href="javascript:void(0)" class="btn btn-danger" rel=' +
        FirstRowId +
        ' onclick="removeSetting(this.rel)"><i class="bi bi-trash"></i></a>';
    $("#" + FirstRowId + "-btn").html(removeButton);

    //  $(".setting").append("<div class='setting-row' id='setting-row"+NextRow+"' >Hello  <a href='#' class='btn btn-success ' onclick='removeSetting("+NextRow+")'><i class='bi bi-minus-lg'></i> Remove</a></div>");

    services = [];
    $("select[name^='service_id']").each(function (index, val) {
        var id = this.id;
        var row_id = parseInt(id);
        product_id = $("#" + row_id + "-service_id").val();
        services.push(product_id);
    });

     console.log(services);
    $.ajax({
        type: "get",

        url: "/add-new-service",

        data: {
            new_row: NextRow,
            totalrecord: totalrecord,
            services: services,
        },
        dataType: "html",

        success: function (data) {
            $("#" + FirstRowId + "-btn").html(removeButton);
            $(".setting").append(data);
             $(".select2").select2();
            calcualtePrice();
        },
    });
}

function removeSetting(id) {
    $("#" + id).remove();
    allproducts = [];
    $("select[name^='service_id']").each(function (index, val) {
        var id = this.id;
            var row_id = parseInt(id);
            product_id = $("#" + row_id + "-service_id").val();
            allproducts.push(product_id);

    });

    totalrows = $(".setting > .setting-row").length;
    totalrecord = $(".totalrecord-settings").length;
    var div = $(".setting > .setting-row:last");
    FirstRowId = div.attr("id");
    console.log(FirstRowId);
    lastRow = FirstRowId.split("setting-row");

    $.ajax({
        type: "post",

        url: "/update-services",

        data: { services: allproducts },
        dataType: "html",

        success: function (data) {
            console.log(data);
            // console.log("#" + lastRow[1] + "-product_id");
            $("#" + lastRow[1] + "-service_id").html(data);

            calcualtePrice();
        },
    });
}


$(function() {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $("#skill_input").autocomplete({
        source: "/get-detail",
        select: function( event, ui ) {
            event.preventDefault();
            $("#skill_input").val(ui.item.value);

            $("#name").val(ui.item.name);
            $("#email").val(ui.item.email);
            $("#phone").val(ui.item.phone);
            $("#whatsapp_number").val(ui.item.phone);





        }
    });
});

$(document).on("change", "#userss, #search, #status,#daterange", function () {
    getPaitentVisit();
});

$(document).on("change", "#phone", function () {

    var phone = $("#phone").val();
    $("#whatsapp_number").val(phone);
});



function getPaitentVisit(){

    var  patientid  = $("#userss").val();
    var  search  = $("#search").val();
    var  status  = $("#status").val();
    var  daterange  = $("#daterange").val();


    $.ajax({
        type: "post",

        url: "/update-patient-history",

        data: { patientid:patientid,search:search,status:status,daterange:daterange },
        dataType: "json",

        success: function (data) {
            // console.log(data);
            $("#history").html(data.html);
             
             setTimeout(function(){ 
  
    $("[data-toggle='toggle']").bootstrapToggle('destroy')                 
    $("[data-toggle='toggle']").bootstrapToggle();
    // $("[data-toggle='switchbutton']").switchButton();
    // alert("S");
  
}, 1000)


            //  $('input[data-toggle="switchbutton"]').bootstrapSwitch();
        },
    });
}
