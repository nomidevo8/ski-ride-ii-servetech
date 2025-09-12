jQuery(document).ready(function ($) {

    // Add Ability Row
    $(".add-row").on("click", function () {
        let index = $("#abilities-table > tbody > tr.ability-row").length;
        let row = `
        <tr class="ability-row" data-ability-index="${index}">
            <td>
                <input type="text" class="form-control" 
                       name="srs_abilities[${index}]">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
        $("#abilities-table > tbody").append(row);
    });

    // Remove Ability Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.ability-row").remove();
        reindexAbilities();
    });

    // Reindex after removal
    function reindexAbilities() {
        $("#abilities-table > tbody > tr.ability-row").each(function (i) {
            $(this).attr("data-ability-index", i);

            $(this).find("input").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_abilities\[\d+]/, `srs_abilities[${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }
});
