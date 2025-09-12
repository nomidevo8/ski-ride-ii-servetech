jQuery(document).ready(function ($) {

    // Add Renting Option Row
    $(".add-row").on("click", function () {
        let index = $("#renting-options-table > tbody > tr.option-row").length;
        let row = `
        <tr class="option-row" data-option-index="${index}">
            <td>
                <input type="text" class="form-control" 
                       name="srs_renting_options[${index}]">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
        $("#renting-options-table > tbody").append(row);
    });

    // Remove Renting Option Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.option-row").remove();
        reindexOptions();
    });

    // Reindex after removal
    function reindexOptions() {
        $("#renting-options-table > tbody > tr.option-row").each(function (i) {
            $(this).attr("data-option-index", i);

            $(this).find("input").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_renting_options\[\d+]/, `srs_renting_options[${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }
});
