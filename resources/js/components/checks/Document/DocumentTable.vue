<template>
  <div class="table-responsive">
    <table class="table table-striped table-hover mt-3">
      <thead>
        <tr>
          <th v-for="(header, index) in headers" :key="index">{{ header }}</th>
        </tr>
      </thead>
      <tbody v-if="data">
        <tr v-if="data.length == 0">
          <td :colspan="headers.length" class="text-center">
            No se encontraron registros por mostrar.
          </td>
        </tr>
        <tr v-for="(value, index) in data" :key="value.id">
          <td>{{ index + 1 }}</td>
          <td>{{ value.code }}</td>
          <td>{{ value.document_name }}</td>
          <td>{{ value.account_number }}</td>
          <td>{{ value.initial_amount }}</td>
          <td>{{ value.name_bank }}</td>
          <td>
            <a href="#" class="btn btn-warning" @click="edit(value.id)"
              ><i class="fa fa-edit"></i> Editar</a
            >
            <a href="#" class="btn btn-danger" @click="deleteValue(value.id)"
              ><i class="fa fa-trash"></i> Eliminar</a
            >
            <a href="#" class="btn btn-info" @click="generateReport(value.id)">
              <i class="fa fa-doc"></i> Reporte</a
            >
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    headers: {
      type: Array,
      required: false,
      default: () => [],
    },
    data: {
      type: Array,
      required: true,
      default: () => [],
    },
  },
  mounted() {
    console.log(this.data);
  },
  methods: {
    async deleteValue(id) {
      this.$emit("delete", id);
    },
    async edit(id) {
      this.$emit("edit", id);
    },
    generateReport(id) {
      window.open("/generateSummary/" + id);
    },
  },
};
</script>

<style>
</style>
