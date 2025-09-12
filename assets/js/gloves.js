jQuery(document).ready(function ($) {

    // Add Glove Row
    $(".add-row").on("click", function () {
        let index = $("#gloves-table > tbody > tr.gear-row").length;

        // Build renting options HTML
        let rentingHtml = "";
        if (srsGloves && srsGloves.rentingOptions && srsGloves.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_gloves[gears][${index}][renting_options][]" multiple>`;
            srsGloves.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsGloves.noOptionsMsg}</div>`;
        }

        let row = `
        <tr class="gear-row" data-gear-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_gloves[gears][${index}][name]">
                <input type="hidden" name="srs_gloves[gears][${index}][product_id]" value="0">
            </td>

            <td>
                <textarea class="form-control" rows="2" name="srs_gloves[gears][${index}][desc]"></textarea>
            </td>

            <td>
                <table class="table table-sm table-bordered day-prices-table mb-2">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-sm btn-primary add-day">Add Day</button>
            </td>

            <td>
                <input type="number" step="0.01" class="form-control" name="srs_gloves[gears][${index}][prices][extra]">
            </td>

            <td>
                ${rentingHtml}
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;

        $("#gloves-table > tbody").append(row);
    });

    // Remove Glove Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.gear-row").remove();
        reindexGears();
    });

    // Add Day Row (for a specific glove)
    $(document).on("click", ".add-day", function () {
        let gearRow = $(this).closest("tr.gear-row");
        let gearIndex = gearRow.data("gear-index");
        let tbody = gearRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_gloves[gears][${gearIndex}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_gloves[gears][${gearIndex}][prices][value][]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-day">×</button></td>
        </tr>`;
        tbody.append(row);
    });

    // Remove Day Row
    $(document).on("click", ".remove-day", function () {
        $(this).closest("tr").remove();
    });

    // Reindex gloves after removal to keep names consistent
    function reindexGears() {
        $("#gloves-table > tbody > tr.gear-row").each(function (i) {
            $(this).attr("data-gear-index", i);

            // Update inputs/selects/textarea inside this glove row
            $(this).find("input, select, textarea").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    // replace the '[gears][<index>]' portion with the new index
                    name = name.replace(/srs_gloves\[gears]\[\d+]/, `srs_gloves[gears][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }

});
