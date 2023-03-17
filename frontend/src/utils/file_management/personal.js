import FileManagement from "./FileManagement";

export default class Personal extends FileManagement {
  #context;
  constructor(context = null) {
    super(context);
    this.#context = context;
  }
}
