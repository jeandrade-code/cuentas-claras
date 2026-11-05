
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe financiero - Cuentas Claras</title>

    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #34423D;
            line-height: 1.5;
        }

        .encabezado {
            background-color: #006948;
            color: #FFFFFF;
            padding: 22px;
        }

        .marca {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .titulo {
            font-size: 23pt;
            font-weight: bold;
            margin-top: 15px;
        }

        .subtitulo {
            font-size: 10pt;
            color: #E5F2EC;
        }

        .periodo {
            margin-top: 15px;
            font-size: 10pt;
        }

        h2 {
            color: #006948;
            font-size: 14pt;
            margin-top: 28px;
            margin-bottom: 12px;
            border-bottom: 2px solid #DCE9E3;
            padding-bottom: 7px;
        }

        p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .indicadores td {
            width: 50%;
            padding: 13px;
            border: 5px solid #FFFFFF;
            background-color: #F0F6F3;
        }

        .indicador-label {
            font-size: 9pt;
            color: #61736A;
        }

        .indicador-valor {
            font-size: 15pt;
            font-weight: bold;
            color: #006948;
        }

        .tabla th {
            background-color: #006948;
            color: #FFFFFF;
            text-align: left;
            padding: 10px 8px;
            font-size: 9pt;
        }

        .tabla td {
            border-bottom: 1px solid #DCE5DF;
            padding: 9px 8px;
            font-size: 9pt;
        }

        .tabla tr:nth-child(even) td {
            background-color: #F5F8F6;
        }

        .derecha {
            text-align: right;
        }

        .centro {
            text-align: center;
        }

        .nota {
            background-color: #F0F6F3;
            border-left: 4px solid #006948;
            padding: 12px;
            margin-top: 15px;
            font-size: 9pt;
        }

        .pie {
            margin-top: 35px;
            padding-top: 12px;
            border-top: 1px solid #DCE5DF;
            color: #75847C;
            font-size: 8pt;
            text-align: center;
        }
    </style>
</head>

<body>

    @php
        $simbolo = '$';
        $formatear = fn($valor) => $simbolo . number_format(
            (float) $valor,
            0,
            ',',
            '.'
        );

        $porcentaje = fn($valor) => $valor !== null
            ? number_format((float) $valor, 1, ',', '.') . '%'
            : 'No disponible';
    @endphp

    <table class="encabezado">
        <tr>
            <td>
                <div class="marca">CUENTAS CLARAS</div>

                <div class="titulo">Informe financiero</div>

                <div class="subtitulo">
                    Resumen de tu comportamiento financiero
                </div>

                <div class="periodo">
                    Periodo: <strong>{{ $mes }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <h2>Resumen del periodo</h2>

    <table class="indicadores">
        <tr>
            <td>
                <div class="indicador-label">Ingreso mensual</div>
                <div class="indicador-valor">
                    {{ $formatear($resumen['ingreso'] ?? 0) }}
                </div>
            </td>

            <td>
                <div class="indicador-label">Total gastado</div>
                <div class="indicador-valor">
                    {{ $formatear($resumen['total_gastado'] ?? 0) }}
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="indicador-label">Saldo disponible</div>
                <div class="indicador-valor">
                    {{ $formatear($resumen['saldo_disponible'] ?? 0) }}
                </div>
            </td>

            <td>
                <div class="indicador-label">Porcentaje gastado</div>
                <div class="indicador-valor">
                    {{ ($resumen['ingreso'] ?? 0) > 0
                        ? $porcentaje(
                            (($resumen['total_gastado'] ?? 0) /
                            $resumen['ingreso']) * 100
                        )
                        : 'No disponible' }}
                </div>
            </td>
        </tr>
    </table>

    <h2>Distribución presupuestaria 50/30/20</h2>

    <p>
        Comparación entre la distribución recomendada del presupuesto
        y los valores registrados durante el periodo.
    </p>

    <table class="tabla">
        <thead>
            <tr>
                <th>Distribución</th>
                <th class="derecha">Valor registrado</th>
                <th class="centro">Porcentaje real</th>
                <th class="centro">Recomendado</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($bloques as $bloque)
                <tr>
                    <td>{{ $bloque['nombre'] ?? '' }}</td>

                    <td class="derecha">
                        {{ $formatear(
                            $bloque['monto_real']
                            ?? $bloque['monto_gastado']
                            ?? 0
                        ) }}
                    </td>

                    <td class="centro">
                        {{ $porcentaje(
                            $bloque['porcentaje_real'] ?? null
                        ) }}
                    </td>

                    <td class="centro">
                        {{ $bloque['porcentaje_recomendado'] ?? 0 }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Detalle de gastos por categoría</h2>

    <p>
        Relación de los gastos registrados y su distribución
        dentro del presupuesto.
    </p>

    <table class="tabla">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Distribución</th>
                <th class="derecha">Valor gastado</th>
                <th class="centro">Porcentaje</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($detalle as $item)
                <tr>
                    <td>
                        {{ $item['categoria_nombre'] ?? '' }}
                    </td>

                    <td>
                        {{ ucfirst(
                            $item['bloque'] ?? 'Sin clasificar'
                        ) }}
                    </td>

                    <td class="derecha">
                        {{ $formatear(
                            $item['monto_gastado'] ?? 0
                        ) }}
                    </td>

                    <td class="centro">
                        {{ $porcentaje(
                            $item['porcentaje_real'] ?? null
                        ) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="centro">
                        No hay gastos registrados para este periodo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="nota">
        <strong>Sobre este informe:</strong>
        Los valores presentados corresponden a la información
        registrada en Cuentas Claras para el periodo seleccionado.
        Los porcentajes permiten comparar la distribución real
        con la recomendación presupuestaria 50/30/20.
    </div>

    <div class="pie">
        <strong>CUENTAS CLARAS</strong><br>
        Plataforma de gestión de finanzas personales.<br>
        Documento generado desde la plataforma.
    </div>

</body>
</html>
