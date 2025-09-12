jQuery(document).ready(function ($) {

    // Add Location Row
    $(".add-row").on("click", function () {
        let index = $("#locations-table > tbody > tr.location-row").length;
        let row = `
        <tr class="location-row" data-location-index="${index}">
            <td>
                <input type="text" class="form-control" 
                       name="srs_locations[${index}]">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;
        $("#locations-table > tbody").append(row);
    });

    // Remove Location Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.location-row").remove();
        reindexLocations();
    });

    // Reindex after removal
    function reindexLocations() {
        $("#locations-table > tbody > tr.location-row").each(function (i) {
            $(this).attr("data-location-index", i);

            $(this).find("input").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_locations\[\d+]/, `srs_locations[${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }
});
