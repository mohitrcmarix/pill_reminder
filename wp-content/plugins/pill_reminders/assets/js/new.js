jQuery(document).ready(function ($) {
  // click on plus icon
  $(".btn-plus").on("click", function (e) {
    e.preventDefault();

    var $input = $(this).closest(".dose-wrapper").find(".dose-input");
    var currentVal = parseInt($input.val());

    if (!isNaN(currentVal)) {
      $input.val(currentVal + 1);
    } else {
      $input.val(1);
    }
  });

  // click on minus icon
  $(".btn-minus").on("click", function (e) {
    e.preventDefault();

    var $input = $(this).closest(".dose-wrapper").find(".dose-input");
    var currentVal = parseInt($input.val());

    if (!isNaN(currentVal) && currentVal > 0) {
      $input.val(currentVal - 1);
    } else {
      $input.val(0);
    }
  });

  // reset the add pillreminder form
  // $("#reminderForm").on("submit", function (e) {
  //   // after a short delay, clear the form
  //   var form = this;
  //   setTimeout(function () {
  //     form.reset();
  //   }, 500);
  // });

  // delete the pill reminder
  $(document).on("click", ".delete-reminder", function (e) {
    e.preventDefault();

    var medicineId = $(this).data("id");
    var card = $(this).closest(".col-md-6");
    var rowContainer = card.closest(".row.gy-3");
    var loader = $("#pill-loader");

    if (confirm("Are you sure you want to delete this pill reminder?")) {
      loader.css("display", "flex");
      $.ajax({
        url: ajax_object.ajaxurl,
        type: "POST",
        data: {
          action: "delete_pill_reminder",
          medicine_id: medicineId,
        },
        success: function (response) {
          if (response.success) {
            card.fadeOut(400, function () {
              $(this).remove();
              loader.hide();
              if (rowContainer.find(".col-md-6").length === 0) {
                rowContainer.html(
                  "<div class='col-12'><p class='error-message text-center'>No reminders found.</p></div>",
                );
              }
            });
          } else {
            loader.hide();
            alert("Failed to delete: " + (response.data || "Unknown error"));
          }
        },
        error: function () {
          loader.hide();
          alert("Something went wrong. Please try again.");
        },
      });
    }
  });

  // add new time field
  $(document).on("click", ".add-time-btn", function (e) {
    e.preventDefault();

    var $newRow = $(".reminder-time-row:first").clone();

    $newRow.find("input").val("");

    // Change the button from Add to Remove
    $newRow.find(".col-lg-auto").html(`
            <a href="#" class="font-16 font-weight-500 text-danger remove-time-btn">
                <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12.5" cy="13" r="12.5" fill="#FF2A2A"/>
                    <path d="M13.7065 12.9947L16.5293 10.1719C16.6903 10.0108 16.7808 9.79245 16.7808 9.56473C16.7808 9.33701 16.6903 9.11861 16.5293 8.95759C16.3683 8.79656 16.1499 8.7061 15.9222 8.7061C15.6945 8.7061 15.4761 8.79656 15.315 8.95759L12.4922 11.7804L9.66932 8.96044C9.5083 8.79942 9.2899 8.70896 9.06218 8.70896C8.83446 8.70896 8.61606 8.79942 8.45504 8.96044C8.29401 9.12147 8.20355 9.33986 8.20355 9.56759C8.20355 9.79531 8.29401 10.0137 8.45504 10.1747L11.2779 12.9947L8.4579 15.8176C8.37817 15.8973 8.31492 15.992 8.27177 16.0961C8.22862 16.2003 8.20641 16.312 8.20641 16.4247C8.20641 16.5375 8.22862 16.6491 8.27177 16.7533C8.31492 16.8575 8.37817 16.9521 8.4579 17.0319C8.53763 17.1116 8.63228 17.1749 8.73646 17.218C8.84063 17.2612 8.95228 17.2834 9.06504 17.2834C9.1778 17.2834 9.28945 17.2612 9.39362 17.218C9.4978 17.1749 9.59245 17.1116 9.67218 17.0319L12.4922 14.209L15.315 17.0319C15.4761 17.1929 15.6945 17.2834 15.9222 17.2834C16.1499 17.2834 16.3683 17.1929 16.5293 17.0319C16.6903 16.8708 16.7808 16.6525 16.7808 16.4247C16.7808 16.197 16.6903 15.9786 16.5293 15.8176L13.7065 12.9947Z" fill="white"/>
                </svg>
                <span>Remove</span>
            </a>
        `);

    $(".reminder-time-row:last").after($newRow);
  });

  // remove added time field
  $(document).on("click", ".remove-time-btn", function (e) {
    e.preventDefault();
    $(this).closest(".reminder-time-row").remove();
  });

  // hide show password
  $("#togglePassword").on("click", function () {
    console.log("Password toggle clicked");

    const input = $("#password");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);

    $(this).text(type === "password" ? "Show" : "Hide");
  });

  //   hide show confirmpassword
  $("#toggleConfirmPassword").on("click", function () {
    console.log("Confirm password toggle clicked");

    const input = $("#confirmPassword");
    const type = input.attr("type") === "password" ? "text" : "password";
    input.attr("type", type);

    $(this).text(type === "password" ? "Show" : "Hide");
  });

  //validation on form submit
  $("form").on("submit", function (e) {
    let isValid = true;
    $(".error-msg").remove();

    // Title
    if ($("input[name='title']").val().trim() === "") {
      $("input[name='title']").after(
        "<span class='error-msg text-danger'>Title is required</span>",
      );
      isValid = false;
    }

    // Medicine Name
    if ($("input[name='medicine_name']").val().trim() === "") {
      $("input[name='medicine_name']").after(
        "<span class='error-msg text-danger'>Medicine name is required</span>",
      );
      isValid = false;
    }

    // Dose Value
    let doseVal = $("input[name='dose_value']").val();
    if (doseVal === "" || isNaN(doseVal) || doseVal <= 0) {
      $("input[name='dose_value']").after(
        "<span class='error-msg text-danger'>Enter a valid dose</span>",
      );
      isValid = false;
    }

    // Dose Type
    if ($("select[name='dose_type']").val() === "") {
      $("select[name='dose_type']").after(
        "<span class='error-msg text-danger'>Select dose type</span>",
      );
      isValid = false;
    }

    // Frequency
    if ($("select[name='frequency']").val() === "") {
      $("select[name='frequency']").after(
        "<span class='error-msg text-danger'>Select frequency</span>",
      );
      isValid = false;
    }

    // Duration
    if ($("select[name='duration_type']").val() === "") {
      $("select[name='duration_type']").after(
        "<span class='error-msg text-danger'>Select duration type</span>",
      );
      isValid = false;
    }
    if (
      $("input[name='duration_value']").val() === "" ||
      $("input[name='duration_value']").val() <= 0
    ) {
      $("input[name='duration_value']").after(
        "<span class='error-msg text-danger'>Enter valid duration</span>",
      );
      isValid = false;
    }

    // Instruction
    if ($("select[name='instruction']").val() === "") {
      $("select[name='instruction']").after(
        "<span class='error-msg text-danger'>Select instruction</span>",
      );
      isValid = false;
    }
     if (
      $("input[name='instruction_time']").val() === "" ||
      $("input[name='instruction_time']").val() <= 0
    ) {
      $("input[name='instruction_time']").after(
        "<span class='error-msg text-danger'>Enter valid duration</span>",
      );
      isValid = false;
    }

    // Dates
    let fromDate = $("input[name='from_date']").val();
    let toDate = $("input[name='to_date']").val();
    if (fromDate === "") {
      $("input[name='from_date']").after(
        "<span class='error-msg text-danger'>Select start date</span>",
      );
      isValid = false;
    }
    if (toDate === "") {
      $("input[name='to_date']").after(
        "<span class='error-msg text-danger'>Select end date</span>",
      );
      isValid = false;
    }
    if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
      $("input[name='to_date']").after(
        "<span class='error-msg text-danger'>End date must be after start date</span>",
      );
      isValid = false;
    }

    // Email
    let email = $("input[name='email']").val();
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email === "" || !emailPattern.test(email)) {
      $("input[name='email']").after(
        "<span class='error-msg text-danger'>Enter valid email</span>",
      );
      isValid = false;
    }

    // Reminder Times
    $("input[name='times[]']").each(function () {
      if ($(this).val() === "") {
        $(this).after(
          "<span class='error-msg text-danger'>Time is required</span>",
        );
        isValid = false;
      }
    });

    if (!isValid) {
      e.preventDefault(); // stop form submission
    }
  });
  //current date to past date disable
  var today=new Date().toISOString().split('T')[0];
  $("input[name=from_date]").attr('min',today);
  $('input[name=to_date]').attr('min',today);

  // $('.toggle-status').on('change', function() {
  //     let checkbox = $(this);
  //     let currentStatus = checkbox.is(':checked') ? 1 : 0;
  //     let medicineId = checkbox.data('medicine-id'); // Add medicine ID in HTML

  //     // Update text label
  //     let statusText = currentStatus ? 'Active' : 'Deactive';
  //     checkbox.closest('h4').find('span').text(statusText);

  //     // Change background color accordingly
  //     if(currentStatus){
  //         checkbox.closest('h4').find('span').css({
  //             'background-color':'#CFF3D1',
  //             'color':'#388E3C'
  //         });
  //     } else {
  //         checkbox.closest('h4').find('span').css({
  //             'background-color':'#F3D1D1',
  //             'color':'#8E3C3C'
  //         });
  //     }

  //     // Send AJAX request to update DB
  //     $.ajax({
  //         url: ajax_object.ajaxurl, // WordPress provides this global variable
  //         type: 'POST',
  //         data: {
  //             action: 'update_medicine_status',
  //             medicine_id: medicineId,
  //             status: currentStatus
  //         },
  //         success: function(response) {
  //             console.log('Status updated:', response);
  //         }
  //     });
  // });
});
