<template>
  <div>
    <div class="container">
      <template v-if="!loading">
        <div class="row">
          <h1>Cuentas</h1>
          <!-- Form -->
          <div class="col-md-4 d-none">
            <input class="form-control" type="text" v-model="document.id" />
          </div>

          <div class="col-md-4 pt-3">
            <label>Código</label>
            <input class="form-control" type="text" v-model="document.code" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Nombre</label>
            <input
              class="form-control"
              type="text"
              v-model="document.document_name"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>Banco</label>
            <select v-model="document.name_bank" class="form-select">
              <option
                v-for="bank in banks"
                :key="bank.id"
                :value="bank.name_bank"
              >
                {{ bank.name_bank }}
              </option>
            </select>
          </div>
          <div class="col-md-4 pt-3">
            <label>No. de cuenta</label>
            <input
              class="form-control"
              type="text"
              v-model="document.account_number"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>Monto inicial (US$)</label>
            <input
              class="form-control"
              type="number"
              step="0.01"
              v-model="document.initial_amount"
            />
          </div>

          <div class="col-md-12 pt-3">
            <a href="#" class="btn btn-success" @click="save()">
              <i class="fa fa-save"></i> {{ textButton }}</a
            >
          </div>

          <!-- Table -->
          <document-table
            :data="documents"
            :headers="headers"
            @delete="deleteP($event)"
            @edit="edit($event)"
          />
        </div>
        <paginationLaravel
          :data="pagination"
          @pagination-change-page="getResults"
          :limit="1"
        ></paginationLaravel>
      </template>
      <template v-else>
        <alert />
      </template>
    </div>
  </div>
</template>

<script>
import ui from "../../../libs/ui";
import axios from "axios";
import Swal from "sweetalert2";
import PaginationLaravel from "laravel-vue-pagination";

export default {
  components: { PaginationLaravel },
  data: () => {
    return {
      document: {
        code: "",
        document_name: "",
        account_number: "",
        name_bank: "",
        initial_amount: "0.00",
      },
      banks: [],
      documents: [],
      headers: [
        "#",
        "Código",
        "Nombre",
        "No. de cuenta",
        "Monto inicial",
        "Banco",
        "Acciones",
      ],
      textButton: "Guardar",
      loading: false,
      pagination: {},
    };
  },

  mounted() {
    this.initialize();
  },

  methods: {
    async initialize() {
      this.loading = true;

      let res = await axios.get("api/document");
      this.documents = res.data.documents.data;
      this.pagination = res.data.documents;

      res = await axios.get("api/bank");
      this.banks = res.data.banks;

      if (this.banks.length > 0) {
        this.document.name_bank = res.data.banks[0].name_bank;
      }

      this.loading = false;
    },

    async save() {
      let res;
      switch (this.textButton) {
        case "Guardar":
          res = await axios.post("api/document", this.document).catch((e) => {
            ui.alert("Registro no pudo ser guardado correctamente.", "error");
          });

          if (res.data.message == "success") {
            this.documents = res.data.documents;
            ui.alert("Registro creado correctamente.");
          }
          break;
        case "Modificar":
          res = await axios
            .put(`api/document/${this.document.id}`, this.document)
            .catch((e) => {
              ui.alert(
                "Registro no pudo ser actualizado correctamente.",
                "error"
              );
            });

          if (res.data.message == "success") {
            ui.alert("Registro modificado correctamente.");
          }
          break;
      }
      this.initialize();
      this.cleanInputs();
    },

    edit(id) {
      this.document = this.documents.find((document) => document.id == id);
      this.textButton = "Modificar";
    },

    async deleteP(id) {
      Swal.fire({
        title: "¿Estás seguro de eliminar este registro?",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Confimar",
      }).then(async (result) => {
        if (result.isConfirmed) {
          const res = await axios.delete(`api/document/${id}`).catch((e) => {
            ui.alert("Registro no pudo ser eliminado correctamente.", "error");
          });

          if (res.data.message == "success") {
            ui.alert("Registro eliminado correctamente.");
            this.initialize();
            this.cleanInputs();
          }
        }
      });
    },

    cleanInputs() {
      this.document = {
        code: "",
        document_name: "",
        account_number: "",
        name_bank: "",
        initial_amount: "0.00",
      };
      this.textButton = "Guardar";
    },

    async getResults(page = 1) {
      const res = await axios.get(this.pagination.path + "?page=" + page);
      this.documents = res.data.documents.data;
      this.pagination = res.data.documents;
    },
  },
};
</script>

<style>
</style>
