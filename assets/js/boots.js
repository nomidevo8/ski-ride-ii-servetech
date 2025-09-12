jQuery(document).ready(function ($) {

    // Add Boot Row
    $(".add-row").on("click", function () {
        let index = $("#boots-table > tbody > tr.boot-row").length;

        let rentingHtml = "";
        if (srsBoots && srsBoots.rentingOptions && srsBoots.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_boots[boots][${index}][renting_options][]" multiple>`;
            srsBoots.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsBoots.noOptionsMsg}</div>`;
        }

        
        // Type Options (Child / Adult)
        let typeHtml = `<select class="form-select" name="srs_boots[boots][${index}][type_options][]" multiple>
                            <option value="Child">Child</option>
                            <option value="Adult">Adult</option>
                        </select>`;

        let row = `
        <tr class="boot-row" data-boot-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_boots[boots][${index}][name]">
                <input type="hidden" name="srs_boots[boots][${index}][product_id]" value="0">
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
                <input type="number" step="0.01" class="form-control" name="srs_boots[boots][${index}][prices][extra]">
            </td>
            <td>
                ${rentingHtml}
            </td>

            <td>
                ${typeHtml}
            </td>

            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
            </td>
        </tr>`;

        $("#boots-table > tbody").append(row);
    });

    // Remove Boot Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.boot-row").remove();
        reindexBoots();
    });

    // Add Day Row
    $(document).on("click", ".add-day", function () {
        let bootRow = $(this).closest("tr.boot-row");
        let index = bootRow.data("boot-index");
        let tbody = bootRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_boots[boots][${index}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_boots[boots][${index}][prices][value][]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-day">×</button></td>
        </tr>`;
        tbody.append(row);
    });

    // Remove Day Row
    $(document).on("click", ".remove-day", function () {
        $(this).closest("tr").remove();
    });

    // Reindex boot rows after removal
    function reindexBoots() {
        $("#boots-table > tbody > tr.boot-row").each(function (i) {
            $(this).attr("data-boot-index", i);

            $(this).find("input, select, textarea").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_boots\[boots]\[\d+]/, `srs_boots[boots][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }

});
