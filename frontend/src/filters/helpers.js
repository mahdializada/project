import Vue from "vue";

Vue.filter("getFileSize", (bytes, decimals = 0) => {
  if (bytes <= 0 || bytes == null) return "0 B";
  const k = 1024;
  const dm = decimals < 0 ? 0 : decimals;
  const sizes = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
});

Vue.filter("getFilenameSubstr", (value, len = 38) => {
  if (!value) return "";
  const name = value.substring(0, value.lastIndexOf("."));
  const extension = value.substring(value.lastIndexOf("."));
  let subText = name?.substring(0, len);
  if (name?.length > 35) subText += "...";
  return subText + extension;
});

Vue.filter("getUserFullName", (user) => {
  if (!user) return "";
  return user.firstname + " " + user.lastname;
});
