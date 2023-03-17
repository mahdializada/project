import {
  SET_ITEMS,
  SET_EXTRA_DATA,
  TOGGLE_API_CALLING,
  INSERT_ITEM,
  UPDATE_ITEM,
  REMOVE_ITEM,
  SET_SUPPLIER_INSERT_DATA
} from "./mutations";

const actions = {
  async fetchSuppliers({ commit }, data) {
    try {
      commit(TOGGLE_API_CALLING, true);
      const response = await this.$axios.get(`suppliers?key=${data.key}`, {
        params: data.options,
      });
      if (response?.status === 200) {
        commit(TOGGLE_API_CALLING, false);
        commit(SET_ITEMS, response?.data?.data);

        const { data, ...extraData } = response?.data;
        commit(SET_EXTRA_DATA, extraData);
      }
      return response;
    } catch (error) {
      commit(TOGGLE_API_CALLING, false);
      return {error:error};
    }
  },

  updateItem({ commit }, item) {
    commit(UPDATE_ITEM, item);
  },
  // set supplier data for insert
  setSupplierData({ commit }, data) {
    commit(SET_SUPPLIER_INSERT_DATA , data);
  },

  // Insert Data
  async insertSupplier({ commit }, data) {
    try {
      console.log(data);
      const response = await this.$axios.post('suppliers', data);
      if (response?.status == 201 ) {

        // commit(INSERT_ITEM,true);
        return {result:true}
      }

    } catch (error) {
      if (error.response.status == 422) {
        const errors = error.response.data.errors;
        commit(INSERT_ITEM,false);
        return { result: false, errors: errors };
      }
      return {result:false}
    }
  },
  insertNewItem({ commit }, item) {
    commit(INSERT_ITEM, item);
  },
  deleteItem({ commit }, item) {
    commit(REMOVE_ITEM, item);
  },


  //   //Update Data
  //   async updateSupplier({ commit }, data) {
  //     try {
  //       const response = await this.$axios.put('supplier/id', data);
  //       if(response?.status==200){
  //         commit(UPDATE_ITEM, response?.data?.supplier);
  //       }
  //       return { result: true };
  //     } catch (error) {
  //       if (error.response.status == 422) {
  //         const errors = error.response.data.errors;
  //         return { result: false, errors: errors };
  //       }
  //     }
  //   },

  //   //delete data
  //   async deleteSupplier({commit},data) {
  //     try {
  //       const response = await this.$axios.delete('supplier/id',data);
  //         if(response?.status==200){
  //           commit(DELETE_DATA, response?.data?.supplier);
  //           return { result: true };
  //         }
  //       }catch (error) {
  //         if (error.response.status == 422) {
  //             const errors = error.response.data.errors;
  //             return { result: false, errors: errors };
  //         }
  //         }
  //       },
};

export default actions;
