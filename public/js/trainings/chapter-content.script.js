$(function () {
  $("#add-subchapter-btn").click(function () {
    // show add subchapter title input
    $("#subchapter-title-input").removeClass("d-none");
    // show submit subchapter title button
    $("#submit-btn-group").removeClass("d-none").addClass("d-flex");
    // hide add subchapter title button
    $("#add-subchapter-btn").addClass("d-none");
    // hide back button
    $("#back-btn").addClass("d-none");
    // hide no subchapters found paragraph
    $("#no_subchapters_found_p").addClass("d-none");
  });

  $("#cancel-create-subchapter-btn").click(function () {
    // hide add subchapter title input
    $("#subchapter-title-input").addClass("d-none");
    // hide submit subchapter title button
    $("#submit-btn-group").removeClass("d-flex").addClass("d-none");
    // show add subchapter title button
    $("#add-subchapter-btn").removeClass("d-none");
    // show back button
    $("#back-btn").removeClass("d-none");
    // show no subchapters found paragraph
    $("#no_subchapters_found_p").removeClass("d-none");
  });

  const dropdownBtns = $("button.dropdown-toggle");
  const dropdownMenus = $("ul.dropdown-menu");
  const cancelCreateParagraphBtns = $("input[name='cancel_create_paragraph']");
  const cancelUploadFileBtns = $("input[name='cancel_upload_file']");
  const cancelCreateLinkBtns = $("input[name='cancel_create_link']");
  const cancelCreateListBtns = $("input[name='cancel_create_list']");
  const updateSubchapterBtns = $("button.edit-subchapter-btn");
  const cancelUpdateSubchapterBtns = $("input[name='cancel_update_btn']");
  const editContentBtns = $("input[name='edit_content']");
  const cancelUpdateSubchapterTitleBtns = $(
    "input[name='cancel-edit-subchapter-title-btn']"
  );
  const addListBtns = $("button.add-list-btn");
  let listContentInput = $("input[name='list_content']");
  const listContainer = $(".list-container");
  const removeListBtn = $("i.remove-list");

  removeListBtn.click(function () {
    const listContent = this.id.split("_")[2];
    $("li[id='update_list_content_" + listContent + "']").remove();
  });

  addListBtns.click(function () {
    let subchapterId = "";
    let currItem = this;
    let textInput = null;
    this.classList.forEach(function (e) {
      if (e == "update-unordered-list-btn") {
        listContentInput = $(".update-unordered-list-input");
      } else if (e == "update-ordered-list-btn") {
        listContentInput = $(".update-ordered-list-input");
      } else if (e == "new-list-btn") {
        subchapterId = currItem.id.split("_")[2];
        textInput = $("#list_content_" + subchapterId);
      }
    });

    if (textInput.val()) {
      function deleteCurrItem(id) {
        console.log(id);
      }

      const listInput =
        "<input type='hidden' name='list_content[]' value='" +
        textInput.val() +
        "' />";
      listContainer.append(listInput);
      const listComponent =
        "<li id='list_component_" +
        textInput.val() +
        "'>" +
        textInput.val() +
        " <i id='remove_list_" +
        textInput.val() +
        "' class='fas fa-trash text-danger' style='cursor: pointer'></i></li>";
      listContainer.append(listComponent);

      textInput.val("");
    }
  });

  editContentBtns.click(function () {
    const currId = $(this)[0].id.split("-")[2];

    const editInputId = "#edit_inputs_" + currId;
    const subChapContent = "#subchapter_content_" + currId;
    const subChapActBtns = "#content_action_btns_" + currId;

    // unhide edit inputs
    $(editInputId).removeClass("d-none");

    // hide subchapter content & content action buttons
    $(subChapContent).addClass("d-none");
    $(subChapActBtns).addClass("d-none");
  });

  updateSubchapterBtns.each(function () {
    $(this).click(function () {
      // get edit form and title ids
      const schEditFormId =
        "#edit-subchapter-title-" + $(this)[0].id.split("-")[3];
      const titleId = "#subchapter-title-" + $(this)[0].id.split("-")[3];

      // unhide edit form
      $(schEditFormId).removeClass("d-none");

      // hide subchapter title
      $(titleId).addClass("d-none");
    });
  });

  cancelUpdateSubchapterBtns.each(function () {
    $(this).click(function () {
      const currId = $(this)[0].id.split("-")[3];

      const editInputId = "#edit_inputs_" + currId;
      const subChapContent = "#subchapter_content_" + currId;
      const subChapActBtns = "#content_action_btns_" + currId;

      // unhide edit inputs
      $(editInputId).addClass("d-none");

      // hide subchapter content & content action buttons
      $(subChapContent).removeClass("d-none");
      $(subChapActBtns).removeClass("d-none");
    });
  });

  cancelUpdateSubchapterTitleBtns.each(function () {
    $(this).click(function () {
      const currId = this.id.split("_")[2];
      // hide subchapter title edit form
      $("#edit-subchapter-title-" + currId).addClass("d-none");
      // unhide subchapter title
      $("#subchapter-title-" + currId).removeClass("d-none");
    });
  });

  $("input[type='button']").each(function (e) {
    $(this).click(function () {
      // output id
      const inputType = this.id.split("-")[1];
      const tschId = this.id.split("-")[2];
      if (inputType === "paragraph") {
        // get textarea id
        const textareaId = "#subchapter-paragraph-" + tschId;
        // unhide textarea
        $(textareaId).removeClass("d-none");
        // hide dropdown button and dropdown menu
        dropdownBtns.each(function () {
          if ($(this)["0"].id === "dropdown-btn-" + tschId) {
            $(this).addClass("d-none");
          }
        });
        dropdownMenus.each(function () {
          if ($(this)["0"].id === "dropdown-menu-" + tschId) {
            $(this).addClass("d-none");
          }
        });
      } else if (inputType === "file") {
        // get get uploadForm id
        const uploadFormId = "#subchapter-file-" + tschId;
        // unhide uploadForm
        $(uploadFormId).removeClass("d-none");
        // hide dropdown button and dropdown menu
        dropdownBtns.each(function () {
          if ($(this)["0"].id === "dropdown-btn-" + tschId) {
            $(this).addClass("d-none");
          }
        });
        dropdownMenus.each(function () {
          if ($(this)["0"].id === "dropdown-menu-" + tschId) {
            $(this).addClass("d-none");
          }
        });
      } else if (inputType === "link") {
        // get input form id
        const linkInputId = "#subchapter-link-" + tschId;
        // unhide link input form
        $(linkInputId).removeClass("d-none");
        // hide dropdown button and dropdown menu
        dropdownBtns.each(function () {
          if ($(this)["0"].id === "dropdown-btn-" + tschId) {
            $(this).addClass("d-none");
          }
        });
        dropdownMenus.each(function () {
          if ($(this)["0"].id === "dropdown-menu-" + tschId) {
            $(this).addClass("d-none");
          }
        });
      } else if (inputType === "lists") {
        // get input form id
        const listInputId = "#subchapter-list-" + tschId;
        // unhide link input form
        $(listInputId).removeClass("d-none");
        // hide dropdown button and dropdown menu
        dropdownBtns.each(function () {
          if ($(this)["0"].id === "dropdown-btn-" + tschId) {
            $(this).addClass("d-none");
          }
        });
        dropdownMenus.each(function () {
          if ($(this)["0"].id === "dropdown-menu-" + tschId) {
            $(this).addClass("d-none");
          }
        });
      }
    });
  });

  cancelCreateParagraphBtns.each(function () {
    $(this).click(function () {
      const tschId = $(this)["0"].id.split("-")[3];

      // get textarea id
      const textareaId = "#subchapter-paragraph-" + tschId;
      // hide textarea
      $(textareaId).addClass("d-none");
      // unhide dropdown button and dropdown menu
      dropdownBtns.each(function () {
        if ($(this)["0"].id === "dropdown-btn-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
      dropdownMenus.each(function () {
        if ($(this)["0"].id === "dropdown-menu-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
    });
  });

  cancelUploadFileBtns.each(function () {
    $(this).click(function () {
      const tschId = $(this)["0"].id.split("-")[3];

      // get textarea id
      const uploadInputId = "#subchapter-file-" + tschId;
      // hide textarea
      $(uploadInputId).addClass("d-none");
      // unhide dropdown button and dropdown menu
      dropdownBtns.each(function () {
        if ($(this)["0"].id === "dropdown-btn-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
      dropdownMenus.each(function () {
        if ($(this)["0"].id === "dropdown-menu-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
    });
  });

  cancelCreateLinkBtns.each(function () {
    $(this).click(function () {
      const tschId = $(this)["0"].id.split("-")[3];

      // get text input id
      const uploadInputId = "#subchapter-link-" + tschId;
      // hide text input id
      $(uploadInputId).addClass("d-none");
      // unhide dropdown button and dropdown menu
      dropdownBtns.each(function () {
        if ($(this)["0"].id === "dropdown-btn-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
      dropdownMenus.each(function () {
        if ($(this)["0"].id === "dropdown-menu-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
    });
  });

  cancelCreateListBtns.each(function () {
    $(this).click(function () {
      const tschId = $(this)["0"].id.split("-")[3];

      // get text input id
      const uploadInputId = "#subchapter-list-" + tschId;
      // hide text input id
      $(uploadInputId).addClass("d-none");
      // unhide dropdown button and dropdown menu
      dropdownBtns.each(function () {
        if ($(this)["0"].id === "dropdown-btn-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
      dropdownMenus.each(function () {
        if ($(this)["0"].id === "dropdown-menu-" + tschId) {
          $(this).removeClass("d-none");
        }
      });
    });
  });
});
