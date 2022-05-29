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
        <tr v-for="value in data" :key="value.id">
          <td>{{ value.id }}</td>
          <td>{{ value.check_number }}</td>
          <td>{{ value.name_supplier }}</td>
          <td>{{ value.concept }}</td>
          <td>{{ value.date }}</td>
          <td>{{ value.amount }}</td>
          <td>{{ value.net_total }}</td>
          <td>{{ value.document_name }}</td>
          <td>{{ value.account_number }}</td>
          <td>{{ value.number_project }}</td>
          <td>
            <a href="#" class="btn btn-warning" @click="edit(value)"
              ><i class="fa fa-edit"></i> Editar</a
            >
            <a href="#" class="btn btn-danger" @click="deleteValue(value.id)"
              ><i class="fa fa-trash"></i> Eliminar</a
            >
            <a href="#" class="btn btn-info" @click="generateCheck(value.id)"
              ><i class="fa fa-file"></i> Generar cheque</a
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
    async edit(value) {
      this.$emit("edit", value);
    },
    generateCheck(id) {
      window.open("/generateCheck/" + id);
    },
  },
};
</script>

<style>
</style>
