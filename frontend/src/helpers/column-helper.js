export default {
  // return filtered header
  getselectedHeader(headers, selected_columns) {
    let selected_header = [];
    headers.map((row) => (row.select = false));
    if (selected_columns.length > 0) {
      selected_columns.forEach((id) => {
        headers.forEach((row) => {
          if (row.id == id) {
            row.select = true;
            selected_header.push(row);
          }
        });
      });

      return { selected_header: selected_header, header: headers };
    } else {
      return { selected_header: headers, header: headers };
    }
  },

  // return category values
  getCategoryValue(selected_header, category) {
    // reset values
    category = category.map((row) => {
      row.value = 0;
      return row;
    });
    selected_header.forEach((i) => {
      category.forEach((j) => {
        if (i.category_id == j.category_id) {
          j.value++;
        }
        if (j.category_id == 0) {
          j.value = selected_header.length;
        }
      });
    });
    return category;
  },
  //save changes
  saveChanges(data) {},
};
