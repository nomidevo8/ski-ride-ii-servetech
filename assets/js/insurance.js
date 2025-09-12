jQuery(document).ready(function ($) {

    // Add Insurance Row
    $(".add-row").on("click", function () {
        let index = $("#insurance-table > tbody > tr.insurance-row").length;

        let rentingHtml = "";
        if (srsInsurance && srsInsurance.rentingOptions && srsInsurance.rentingOptions.length > 0) {
            rentingHtml = `<select class="form-select" name="srs_insurance[insurances][${index}][renting_options][]" multiple>`;
            srsInsurance.rentingOptions.forEach(function (opt) {
                rentingHtml += `<option value="${opt}">${opt}</option>`;
            });
            rentingHtml += `</select>`;
        } else {
            rentingHtml = `<div class="alert alert-warning p-2">${srsInsurance.noOptionsMsg}</div>`;
        }
        let typeHtml = "";
        if (srsInsurance.typeOptions && srsInsurance.typeOptions.length > 0) {
            typeHtml = `<select class="form-select" name="srs_goggles[insurances][${index}][type_options][]" multiple>`;
            srsInsurance.typeOptions.forEach(function (type) {
                typeHtml += `<option value="${type}">${type}</option>`;
            });
            typeHtml += `</select>`;
        }
        let row = `
        <tr class="insurance-row" data-insurance-index="${index}">
            <td>
                <input type="text" class="form-control" name="srs_insurance[insurances][${index}][name]">
                <input type="hidden" name="srs_insurance[insurances][${index}][product_id]" value="0">
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
                <input type="number" step="0.01" class="form-control" name="srs_insurance[insurances][${index}][prices][extra]">
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

        $("#insurance-table > tbody").append(row);
    });

    // Remove Insurance Row
    $(document).on("click", ".remove-row", function () {
        $(this).closest("tr.insurance-row").remove();
        reindexInsurance();
    });

    // Add Day Row (for a specific insurance row)
    $(document).on("click", ".add-day", function () {
        let insuranceRow = $(this).closest("tr.insurance-row");
        let index = insuranceRow.data("insurance-index");
        let tbody = insuranceRow.find("table.day-prices-table > tbody");
        let row = `
        <tr>
            <td><input type="number" min="1" class="form-control"
                       name="srs_insurance[insurances][${index}][prices][day][]"></td>
            <td><input type="number" step="0.01" class="form-control"
                       name="srs_insurance[insurances][${index}][prices][value][]"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-day">×</button></td>
        </tr>`;
        tbody.append(row);
    });

    // Remove Day Row
    $(document).on("click", ".remove-day", function () {
        $(this).closest("tr").remove();
    });

    // Reindex insurance rows after removal
    function reindexInsurance() {
        $("#insurance-table > tbody > tr.insurance-row").each(function (i) {
            $(this).attr("data-insurance-index", i);

            $(this).find("input, select, textarea").each(function () {
                let name = $(this).attr("name");
                if (name) {
                    name = name.replace(/srs_insurance\[insurances]\[\d+]/, `srs_insurance[insurances][${i}]`);
                    $(this).attr("name", name);
                }
            });
        });
    }

});
