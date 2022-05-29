<template>
  <div>
    <div class="container">
      <template v-if="!loading">
        <div class="row">
          <h1>Cheques</h1>
          <!-- Form -->
          <div class="col-md-4 d-none">
            <input class="form-control" type="text" v-model="check.id" />
          </div>
          <div class="col-md-6 pt-3">
            <label>Proveedor</label>
            <select v-model="check.name_supplier" class="form-select">
              <option
                v-for="supplier in suppliers"
                :key="supplier.id"
                :value="supplier.name_supplier"
              >
                {{ supplier.name_supplier }}
              </option>
            </select>
          </div>
          <div class="col-md-6 pt-3">
            <label>Concepto</label>
            <textarea
              cols="30"
              rows="3"
              class="form-control"
              v-model="check.concept"
            ></textarea>
          </div>
          <div class="col-md-4 pt-3">
            <label>Fecha</label>
            <input class="form-control" type="date" v-model="check.date" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Monto</label>
            <input
              class="form-control"
              type="number"
              step="0.01"
              v-model="check.net_total"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>Cuenta</label>
            <select v-model="check.document_name" class="form-select">
              <option
                v-for="document in documents"
                :key="document.id"
                :value="document.document_name"
              >
                {{ document.document_name }}
              </option>
            </select>
          </div>
          <div class="col-md-4 pt-3">
            <label>No. de proyecto</label>
            <input
              class="form-control"
              type="text"
              v-model="check.number_project"
            />
          </div>
          <div class="col-md-4 pt-3">
            <label>IVA</label>
            <input class="form-control" type="text" v-model="check.iva" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Documento</label>
            <input class="form-control" type="text" v-model="check.document" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Renta</label>
            <input class="form-control" type="text" v-model="check.rent" />
          </div>
          <div class="col-md-4 pt-3">
            <label>Movimiento</label>
            <select v-model="check.movement" class="form-select">
              <option
                v-for="(movement, index) in movements"
                :key="index"
                :value="movement"
              >
                {{ movement }}
              </option>
            </select>
          </div>

          <div class="col-md-12 pt-3">
            <a href="#" class="btn btn-success" @click="save()">
              <i class="fa fa-save"></i> {{ textButton }}</a
            >
          </div>

          <!-- Table -->
          <check-table
            :data="checks"
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
import Swal from "sweetalert2";
import PaginationLaravel from "laravel-vue-pagination";
import axios from "axios";

export default {
  components: { PaginationLaravel },
  data: () => {
    return {
      check: {
        code: "",
        name_supplier: "",
        concept: "",
        date: "",
        account_number: "",
        number_project: "",
        document_name: "",
        net_total: "0.00",
        iva: "0.13",
        rent: "0.00",
        document: "Factura de consumidor final",
      },
      banks: [],
      documents: [],
      suppliers: [],
      checks: [],
      movements: ["Abonar", "Cargar"],
      headers: [
        "#",
        "Código",
        "Proveedor",
        "Concepto",
        "Fecha",
        "Total",
        "Total neto",
        "Fondo",
        "No. de cuenta",
        "No. de proyecto",
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

      let res = await axios.get("api/check");
      this.checks = res.data.checks.data;
      this.pagination = res.data.checks;

      res = await axios.get("api/document");
      this.documents = res.data.documents.data;

      if (this.documents.length > 0) {
        this.check.document_name = this.documents[0].document_name;
      }

      res = await axios.get("api/supplier");
      this.suppliers = res.data.suppliers.data;

      if (this.suppliers.length > 0) {
        this.check.name_supplier = this.suppliers[0].name_supplier;
      }

      this.check.movement = this.movements[0];
      this.check.date = new Date().toISOString().substr(0, 10);
      this.loading = false;
    },

    async save() {
      let res;
      switch (this.textButton) {
        case "Guardar":
          res = await axios.post("api/check", this.check).catch((e) => {
            ui.alert("Registro no pudo ser guardado correctamente.", "error");
          });

          if (res.data.message == "success") {
            this.checks = res.data.checks;
            ui.alert("Registro creado correctamente.");
          }
          break;
        case "Modificar":
          res = await axios
            .put(`api/check/${this.check.id}`, this.check)
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

    edit(value) {
      this.check = value;

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
          const res = await axios.delete(`api/check/${id}`).catch((e) => {
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
      this.check = {
        net_total: "0.00",
        iva: "0.13",
        rent: "0.00",
      };
      this.textButton = "Guardar";
    },

    async getResults(page = 1) {
      const res = await axios.get(this.pagination.path + "?page=" + page);
      this.checks = res.data.checks.data;
      this.pagination = res.data.checks;
    },
  },
};
</script>

<style>
</style>
