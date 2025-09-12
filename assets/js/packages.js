jQuery(document).ready(function ($) {

    // Add Package Row
    $(".add-packages-row").on("click", function () {
        let index = $("#packages-table > tbody > tr.gear-row").length;

        // Build renting options HTML
        let rentingHtml = "";
        if (srsPackages && srsPackages.rentingOptions && srsPackages.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_packages[gears][${index}][renting_options][]" multiple>`;
            srsPackages.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsPackages.noOptionsMsg}</div>`;
        }

        let row = `
        <tr class="gear-row" data-gear-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_packages[gears][${index}][name]">
                <input type="hidden" name="srs_packages[gears][${index}][product_id]" value="0">
            </td>

            <td>
                <textarea class="form-control" rows="2" name="srs_packages[gears][${index}][desc]"></textarea>
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
                <input type="number" step="0.01" class="form-control" name="srs_packages[gears][${index}][prices][extra]">
            </td>

            <td>
                ${rentingHtml}
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove-packages-row">Remove</button>
            </td>
        </tr>`;

        $("#packages-table > tbody").append(row);
    });

    // Remove Package Row
    $(document).on("click", ".remove-packages-row", function () {
        $(this).closest("tr.gear-row").remove();
        reindexPackages();
    });

    // Add Day Row (for a specific package row)
    $(document).on("click", ".add-day", function () {
        let gearRow = $(this).closest("tr.gear-row");
        let gearIndex = gearRow.data("gear-index");
        let tbody = gearRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_packages[gears][${gearIndex}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_packages[gears][${gearIndex}][prices][value][]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-day">×</button></td>
        </tr>`;
        tbody.append(row);
    });

    // Remove Day Row
    $(document).on("click", ".remove-day", function () {
        $(this).closest("tr").remove();
    });

    // Reindex packages after removal
    function reindexPackages() {
        $("#packages-table > tbody > tr.gear-row").each(function (i) {
            $(this).attr("data-gear-index", i);

            $(this).find("input, select, textarea").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_packages\[gears]\[\d+]/, `srs_packages[gears][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }

});
