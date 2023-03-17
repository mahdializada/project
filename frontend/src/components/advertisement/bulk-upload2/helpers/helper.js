import { forEach } from "lodash";
import HandleException from "../../../../helpers/handle-exception";
const reArrangeErrArr = async (errors) => {
  let final_errors = [];
  let returnedData = [];
  errors.forEach(async (e) => {
    if (final_errors[e.column]) {
      final_errors[e.column].row.add(e.row);
      final_errors[e.column].descriptions.add(e.descriptions);
      return;
    }
    final_errors[e.column] = {
      row: new Set().add(e.row),
      descriptions: new Set().add(e.descriptions),
      column: e.column,
    };
  });
  for (const key in final_errors) {
    if (Object.hasOwnProperty.call(final_errors, key)) {
      const element = final_errors[key];
      returnedData.push({
        column: element.column,
        row: sortArr(element.row),
        descriptions: element.descriptions,
      });
    }
  }
  return returnedData;
};

const sortArr = (array) => {
  return Array.from(array).sort(function (a, b) {
    return a - b;
  });
};

const parseRowErrors = (column, row_index, appContext, form) => {
  const error = appContext.validateRule(
    form[column.toLowerCase()],
    appContext.$root,
    parseString(column, " ", "_").toUpperCase()
  );
  return {
    row: row_index,
    column: column,
    descriptions: error.length ? error[0] : [],
  };
};

const isCorrectSheetUploaded = (target_headers, main_headers) => {
  return main_headers.every((h, index) => {
    return index
      ? target_headers["__EMPTY_" + index].toLowerCase() == h.value
      : target_headers["__EMPTY"].toLowerCase() == h.value;
  });
};

const parseString = (col, new_sep, old_sep = " ") => {
  return col.replaceAll(old_sep, new_sep);
};

const parseRowFields = async (selectedHeaders, selectedRows) => {
  let rows = [];
  let new_row = {};
  selectedRows.forEach((row) => {
    forEach(selectedHeaders, (value, key) => {
      new_row[value.toLowerCase()] = row.hasOwnProperty(key) ? row[key] : null;
    });
    rows.push(new_row);
    new_row = {};
  });
  return rows;
};

const parseHeadersAndRows = (rows) => {
  const selectedHeaders = rows.find((r) => r.__rowNum__ == 4);
  const selectedRows = rows.filter((r) => r.__rowNum__ > 4);
  return [selectedHeaders, selectedRows];
};

const validateRowsInFront = async (appContext, selectedRows, form) => {
  let errors = [];
  selectedRows.forEach((row, index) => {
    forEach(row, (value, key) => {
      form[key].$model = value;
      const error = parseRowErrors(key, index + 6, appContext, form);
      if (error.descriptions.length) errors.push(error);
    });
  });
  return errors;
};

const validateRowsInApi = async (appContext, sheetName) => {
  try {
    let errors = [];
    if (sheetName == "ad-template") {
      let ad_ids = appContext.sheets.ad.map((p, index) => {
        return {
          index: index + 6,
          ad_id: p.ad_id,
        };
      });
      if (ad_ids.length) {
        const url = `advertisement/validate-ad-ids`;
        const { data } = await appContext.$axios.get(url, {
          params: {
            rows: JSON.stringify(ad_ids),
          },
        });
        appContext.sheets.connection_data = data.connection_data;
        errors = data.errors;
      }
    }
    return errors;
  } catch (error) {
    HandleException.handleApiException(appContext, error);
  }
};

const parseNullableRows = (rows) => {
  return rows.map((r) => {
    return parseNullableRow(r);
  });
};

const parseNullableRow = (row) => {
  forEach(row, (value, field) => {
    if (value.toLowerCase() == "null") {
      row[field] = null;
    }
  });
  return row;
};

const parseErrorForm = async (appContext, sheetName) => {
  let form = {};
  switch (sheetName) {
    case "connection-&-campaign-template":
      form = appContext.$v.campaign_form;
      break;
    case "ad-set-template":
      form = appContext.$v.ad_set_form;
      break;
    case "ad-template":
      form = appContext.$v.ad_form;
      break;
  }
  return form;
};

const validateRows = async (headers, rows, appContext, sheetName) => {
  // parse rows,
  const parsedRows = await parseRowFields(headers, rows);
  // parse form and sheet name
  const form = await parseErrorForm(appContext, sheetName);
  // errors of front of validation,
  const f_errors = await validateRowsInFront(appContext, parsedRows, form);
  // validate api/backend errors,
  const b_errors = await validateRowsInApi(appContext, sheetName);
  // validate parent child relationship in levels,
  const a_errors = validateParentChild(parsedRows, sheetName, appContext);
  // merge both errors,
  const errors = await reArrangeErrArr([...f_errors, ...b_errors, ...a_errors]);
  switch (sheetName) {
    case "connection-&-campaign-template":
      appContext.sheets.campaign = parseNullableRows(parsedRows);
      appContext.errors.campaign = errors;
      break;
    case "ad-set-template":
      appContext.sheets.ad_set = parseNullableRows(parsedRows);
      appContext.errors.ad_set = errors;
      break;
    case "ad-template":
      appContext.sheets.ad = parseNullableRows(parsedRows);
      appContext.errors.ad = errors;
      break;
  }
  // return whether data is clean or not
  return isErrorFree(appContext) ? false : true;
};

const isParentAvailable = (rows, sheet, column) => {
  let errors = [];
  rows.forEach((r, index) => {
    const isValid = sheet.map((c) => c[column]).includes(r[column]);
    if (!isValid) {
      errors.push({
        row: index + 6,
        column: column,
        descriptions: r[column] + " has no related parent!",
      });
    }
  });
  return errors;
};

const validateParentChild = (rows, sheetName, appContext) => {
  if (sheetName === "ad-set-template") {
    return isParentAvailable(rows, appContext.sheets.campaign, "campaign_id");
  } else if (sheetName === "ad-template") {
    return isParentAvailable(rows, appContext.sheets.ad_set, "ad_set_id");
  }
  return [];
};

const isErrorFree = (appContext) => {
  return (
    appContext.errors.campaign.length ||
    appContext.errors.ad_set.length ||
    appContext.errors.ad.length
  );
};

const onDownload = async (download_url, appContext) => {
  if (!appContext.isFileDownloaded) {
    try {
      appContext.isFileDownloaded = true;
      const response = await appContext.$axios.get(download_url);
      const url = response.data;
      window.location.href = url;
      appContext.isFileDownloaded = false;
      return true;
    } catch (error) {
      HandleException.handleApiException(appContext, error);
    }
    appContext.isFileDownloaded = false;
  }
};

export {
  reArrangeErrArr,
  parseRowErrors,
  isCorrectSheetUploaded,
  parseString,
  parseRowFields,
  parseHeadersAndRows,
  validateRowsInFront,
  validateRows,
  onDownload,
};
