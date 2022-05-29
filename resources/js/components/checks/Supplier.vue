<template>
  <div>
    <div class="container">
      <template v-if="!loading">
        <div class="row">
          <h1>Proveedores</h1>
          <!-- Form -->
          <div class="col-md-4 d-none">
            <input class="form-control" type="text" v-model="supplier.id" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Nombre</label>
            <input
              class="form-control"
              type="text"
              v-model="supplier.name_supplier"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>DUI</label>
            <input
              class="form-control"
              type="text"
              v-model="supplier.dui"
              v-mask="'########-#'"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>NIT</label>
            <input
              class="form-control"
              type="text"
              v-model="supplier.nit"
              v-mask="'####-######-###-#'"
            />
          </div>
          <div class="col-md-12 pt-3">
            <label>Dirección</label>
            <textarea
              cols="12"
              rows="2"
              class="form-control"
              v-model="supplier.address"
            />
          </div>
          <div class="col-md-12 pt-3">
            <a href="#" class="btn btn-success" @click="save()">
              <i class="fa fa-save"></i> {{ textButton }}</a
            >
          </div>

          <!-- Table -->
          <base-table
            :data="suppliers"
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
import ui from "../../libs/ui";
import PaginationLaravel from "laravel-vue-pagination";
import Swal from "sweetalert2";
import axios from "axios";

export default {
  components: { PaginationLaravel },
  data: () => {
    return {
      supplier: {
        dui: "",
        nit: "",
        name_supplier: "",
      },
      suppliers: [],
      grades: [],
      headers: ["#", "Nombre", "DUI", "NIT", "Dirección", "Acciones"],
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

      let res = await axios.get("api/supplier");
      this.suppliers = res.data.suppliers.data;
      this.pagination = res.data.suppliers;

      this.loading = false;
    },

    async save() {
      let res;
      switch (this.textButton) {
        case "Guardar":
          res = await axios.post("api/supplier", this.supplier).catch((e) => {
            ui.alert("Registro no pudo ser guardado correctamente.", "error");
          });

          if (res.data.message == "success") {
            this.suppliers = res.data.suppliers;
            ui.alert("Registro creado correctamente.");
          }
          break;
        case "Modificar":
          res = await axios
            .put(`api/supplier/${this.supplier.id}`, this.supplier)
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
      this.supplier = this.suppliers.find((supplier) => supplier.id == id);
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
          const res = await axios.delete(`api/supplier/${id}`).catch((e) => {
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
      this.supplier = {};
      this.textButton = "Guardar";
    },

    async getResults(page = 1) {
      const res = await axios.get(this.pagination.path + "?page=" + page);
      this.suppliers = res.data.suppliers.data;
      this.pagination = res.data.suppliers;
    },
  },
};
</script>

<style>
</style>
