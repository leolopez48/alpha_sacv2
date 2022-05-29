<template>
  <div>
    <h2>Resumen</h2>
    <table width="100%" border="1px">
      <tr>
        <td>Concepto</td>
        <td>Debe</td>
        <td>Haber</td>
      </tr>
      <tr v-for="detail in debe" :key="detail.id">
        <td>{{ detail.concept }}</td>
        <td>{{ toMoney(detail.net_total) }}</td>
        <td></td>
      </tr>
      <tr v-for="detail in haber" :key="detail.id">
        <td>{{ detail.concept }}</td>
        <td></td>
        <td>{{ toMoney(detail.net_total) }}</td>
      </tr>
      <tr>
        <td><b>Total (US$)</b></td>
        <td>{{ toMoney(totalDebe) }}</td>
        <td>{{ toMoney(totalHaber) }}</td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      debe: [],
      haber: [],
      totalDebe: "0.00",
      totalHaber: "0.00",
    };
  },

  mounted() {
    this.initialize();
  },

  methods: {
    async initialize() {
      let res = await axios.get("api/check/summary");

      if (res) {
        console.log(res);
        this.debe = res.data.debe;
        this.haber = res.data.haber;
        this.totalDebe = res.data.totalDebe;
        this.totalHaber = res.data.totalHaber;
      }
    },

    toMoney(value) {
      return Number.parseFloat(value).toFixed(2);
    },
  },
};
</script>

<style scoped>
table {
  border-spacing: 0;
}

td,
tr {
  text-align: center;
  border: 1px solid black;
}
</style>
