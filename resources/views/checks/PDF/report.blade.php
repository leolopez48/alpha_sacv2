<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Report</title>

</head>
<style>
    * {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    table {
        width: 100%;
        border: 1px solid black;
        border-spacing: 0px;
    }

    td,
    th {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }

    /* tr:nth-child(even) {
        background-color: #dddddd;
        border: 1px solid white;
    } */

    .title {
        text-align: center;
    }

    .text-right {
        text-align: right !important;
    }

    p {
        margin: 0 !important;
        padding: 0 !important;
    }

    table.no-border tr td,
    .no-border td,
    .no-border th,
    .no-border tr {
        border: 0px;
    }

    .no-border {
        border: 0px;
    }

</style>

<body>
    <table>
        <tr>
            <td colspan="4">
                <center>
                    <b>Tesorería municipal Villa El Paraíso</b>
                </center>
            </td>
        </tr>
        <tr>
            <td>Lugar y fecha</td>
            <td> {{ $check->dateLetters }}</td>
            <td class="text-right">Monto</td>
            <td>US${{ number_format($check->amount, 2) }}</td>
        </tr>
        <tr>
            <td>Páguese a la orden de</td>
            <td colspan="3"> {{ $check->name_supplier }}</td>
        </tr>
        <tr>
            <td>La suma de</td>
            <td colspan="3">{{ $check->total_letters }}</td>
        </tr>
        <tr>
            <td colspan="4">
                <br>
                <br>
                <br>
                <br>
            </td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <td>Cheque No.</td>
            <td colspan="3"> {{ $check->check_number }}</td>
        </tr>
        <tr>
            <td>Banco</td>
            <td> {{ $check->name_bank }}</td>
            <td>Cuenta No.</td>
            <td> {{ $check->account_number }}</td>
        </tr>
        <tr>
            <td>Tipo de fondo</td>
            <td colspan="3"> {{ $check->code }} {{ $check->document_name }}</td>
        </tr>
        <tr>
            <td>No. de proyecto</td>
            <td colspan="3"> {{ $check->number_project }}</td>
        </tr>
        <tr>
            <td colspan="4">Concepto</td>
        </tr>
        <tr>
            <td colspan="4"> {{ $check->concept }}</td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td>Documento</td>
            <td>Número</td>
            <td>Valor total</td>
            <td>Ret. renta</td>
            <td>Ret. IVA</td>
            <td>Val. Líquido</td>
        </tr>
        <tr>
            <td> {{ $check->document }}</td>
            <td> {{ $check->id }}</td>
            <td> ${{ number_format($check->amount, 2) }}</td>
            <td> ${{ number_format($check->net_total * $check->rent, 2) }}</td>
            <td> ${{ number_format($check->net_total * $check->iva, 2) }}</td>
            <td> ${{ number_format($check->net_total * $check->rent + $check->net_total * $check->iva + $check->net_total, 2) }}
            </td>

        </tr>

    </table>

    <br>
    <table>
        <tr>
            <td colspan="9">
                <center>CIFRADO PRESUPUESTARIO</center>
            </td>
        </tr>
        <tr>
            <td>AÑO</td>
            <td>CODIGO PRESUPUESTARIO</td>
            <td>INSTITUCIÓN <br> EL PARAÍSO</td>
            <td>A.G.</td>
            <td>U.P.</td>
            <td>L.T.</td>
            <td>F.F.</td>
            <td>DEBE</td>
            <td>HABER</td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
        <tr>
            <td colspan="7"> </td>
            <td> </td>
            <td> </td>
        </tr>

    </table>

    <br>
    <table class="no-border">
        <tr>
            <td><br><br><br><br><br><br><br><br></td>
            <td><br><br><br><br><br><br><br><br></td>
            <td><br><br><br><br><br><br><br><br></td>
            <td><br><br><br><br><br><br><br><br></td>
            <td><br><br><br><br><br><br><br><br></td>
        </tr>
        <tr>
            <td>HECHO POR</td>
            <td>PRESUPUESTARIO</td>
            <td>AUTORIZADO</td>
            <td>VISTO BUENO</td>
            <td>RECIBI</td>
        </tr>
        <tr>
            <td> María Stefany Urbina Alas</td>
            <td> Jaime Alexandre Sibrián Ávalos</td>
            <td> Alexi Antonio Escalante Galdámez</td>
            <td> Hernán Alvarenga Hércules</td>
            <td> </td>
        </tr>
    </table>

    <br>
    <table class="no-border">
        <tr>
            <td colspan="2">
                <center><b>POR:</b> US${{ number_format($check->amount, 2) }}</center>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Recibí de la Tesorería Municipal Villa El Paraíso, Departamento de Chalatenango, la cantidad de:
                {{ $check->total_letters }} de los Estados Unidos de América
                (${{ number_format($check->amount, 2) }}), en concepto
                de {{ $check->concept }}, egreso que hace la tesorería Municipal Villa El Paraíso de la
                Cuenta
                Corriente {{ $check->document_name }} No. {{ $check->account_number }}, {{ $check->name_bank }}
                y descargado del Cifrado del
                Presupuesto Municipal vigente.
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: right">Villa El Paraíso, {{ $check->dateNumberLetters }}</td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td>
                Pago {{ $check->concept }}
            </td>
            <td>
                ${{ number_format($check->amount, 2) }}
            </td>
        </tr>
        <tr>
            <td>
                Menos el {{ $check->renta * 100 }}% de renta
            </td>
            <td>
                ${{ number_format($check->rent, 2) }}
            </td>
        </tr>
        <tr>
            <td>
                Líquido a recibir
            </td>
            <td>
                ${{ number_format($check->amount, 2) }}
            </td>
        </tr>
    </table>

    <br>
    <br>
    <br>
    <br>
    <b>F.________________________</b> <br>
    <p>
        Nombre: {{ $check->name_supplier }} <br>
        DUI: {{ $check->dui }} <br>
        NIT: {{ $check->nit }} <br>
        Dirección: {{ $check->address }}
    </p>

    <br>
    <br>
    <table class="no-border">
        <tr>
            <td><br><br><br><br><br><br></td>
            <td><br><br><br><br><br><br></td>
        </tr>
        <tr>
            <td>AUTORIZADO</td>
            <td>VISTO BUENO</td>
        </tr>
        <tr>
            <td>Alexi Antonio Escalante Galdámez</td>
            <td>Hernán Alvarenga Hércules </td>
        </tr>
        <tr>
            <td> Alcalde Municipal</td>
            <td> Síndico Municipal</td>
        </tr>
    </table>
</body>

</html>
