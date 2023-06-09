$(function () {
  $.fn.swapWith = function (that) {
    var $this = this;
    var $that = $(that);

    // create temporary placeholder
    var $temp = $("<div>");

    // 3-step swap
    $this.before($temp);
    $that.before($this);
    $temp.before($that).remove();

    return $this;
  };

  const flag = (message) => {
    $(`span.error`).addClass("invisible");
    $(`span.${message}.error`).removeClass("invisible");
    return false;
  };

  const validate = (form) => {
    let valid = true;
    if (form.assistant.value == "" && form.query.value == "") {
      valid = flag("empty");
    } else if (form.query.value == "") {
      valid = flag("query");
    } else if (form.assistant.value == "") {
      valid = flag("assistant");
    } else {
      valid = true;
    }
    return valid;
  };

  const ask = (query, callback) => {
    $.ajax({
      method: "POST",
      url: location.href,
      processData: false,
      contentType: false,
      cache: false,
      data: query,
      enctype: "multipart/form-data",
      success: callback,
      error: console.error,
    });
  };

  const reset = (callback) => {
    $.ajax({
      method: "GET",
      url: location.href,
      processData: false,
      contentType: "application/json",
      cache: false,
      data: { reset: true },
      enctype: "multipart/form-data",
      success: callback,
      error: console.error,
    });
  };

  $(document).on("click", "#ask, #reset, #improve, #summarize, #save", (e) => {
    const form = document.getElementById("ai_assistant");

    currentInst = $(e.target).attr("id");
    currentMid = $(e.target).data("id");

    form.message_id.value = currentMid ? currentMid : "";
    form.instruction.value = currentInst;

    if (currentInst == "reset") {
      reset(() => {
        $("#reply").children("div").remove().fadeIn(300);
      });

      return;
    }

    e.preventDefault();

    if (currentInst == "improve") {
      $("#actions > button.active").each((i, el) => {
        $(el).removeClass("active");
      });
      $("#improve")
        .swapWith("#actions button:first-child")
        .text("Improve")
        .addClass("active");
    }

    if (currentInst == "summarize") {
      $("#actions > button.active").each((i, el) => {
        $(el).removeClass("active");
      });
      $("#summarize")
        .swapWith("#actions button:first-child")
        .text("Summarize")
        .addClass("active");
    }

    if (currentInst !== "ask" && currentInst !== "save") {
      if (form.query.value === "") {
        flag(currentInst);
        return;
      }
    }

    if (validate(form)) {
      $(`span.error`).addClass("invisible");
      $("#loading").removeClass("invisible");

      $("#reply")
        .append(
          $(
            `<div class="user message">${form.query.value}</div><div class="clear"></div>`
          )
        )
        .fadeIn(300);

      ask(new FormData(form), (response) => {
        $("#query").val("");

        $("#loading").addClass("invisible");

        const message = JSON.parse(response);

        currentId = message.id;

        $("#actions > button").each((i, el) => {
          $(el).attr("data-id", currentId);
        });

        $(
          `<div id="reply-${message.timestamp}" class="assistant message"></div><div class="clear"></div>`
        ).appendTo("#reply");

        var app = document.getElementById(`reply-${message.timestamp}`);

        var typewriter = new Typewriter(app, {
          loop: false,
          cursor: "",
          delay: 50,
        });

        typewriter
          .typeString(message.content.replace(/(?:\r\n|\r|\n)/g, "<br>"))
          .callFunction(() => {
            $("#actions > button").each((i, el) => {
              $(el).removeClass("invisible").fadeIn(1000);
            });
          })
          .pauseFor(1000)
          .start()
          .callFunction(() => {
            $(".response")
              .stop()
              .animate({ scrollTop: $("#reply")[0].scrollHeight }, 1000);
          });
      });
    }
    return false;
  });
});
