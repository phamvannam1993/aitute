/*
<template>
  {{ $helpers.generateString(7) }}
</template>

<script>
import { inject } from 'vue'
const helpers = inject('helpers')
helpers.generateString(7)
</script>
*/

export default {
    formatTimestampToYMD(timestamp, format = 'yyyy/mm/dd hh:ii:ss') {
        let date = new Date();

        if (typeof timestamp === 'string') {
            date = new Date(timestamp);
        }

        if (typeof timestamp === 'object') {
            date = timestamp;
        }

        const mapObj = {
            yyyy: date.getFullYear(),
            mm: String(date.getMonth() + 1).padStart(2, '0'),
            dd: String(date.getDate()).padStart(2, '0'),
            hh: String(date.getHours()).padStart(2, '0'),
            ii: String(date.getMinutes()).padStart(2, '0'),
            ss: String(date.getSeconds()).padStart(2, '0'),
        };

        return format.replace(/\b(?:yyyy|mm|dd|hh|ii|ss)\b/gi, matched => mapObj[matched]);
    },

    convertHtmlToText(html = '') {
        const tableTexts = [];

        html = html.replace(/<table[^>]*>([\s\S]*?)<\/table>/gi, (match) => {
            const tableText = this.convertTableToText(match);
            tableTexts.push(tableText);
            return `\n[[TABLE_${tableTexts.length - 1}]]\n`;
        });

        html = html.replace(/<h[1-6][^>]*>(.*?)<\/h[1-6]>/gi, '\n$1\n');
        html = html.replace(/<p[^>]*>(.*?)<\/p>/gi, '$1\n');
        html = html.replace(/<\/?(ul|ol)[^>]*>/gi, '\n');
        html = html.replace(/<li[^>]*>(.*?)<\/li>/gi, '- $1\n');
        html = html.replace(/<br\s*\/?>/gi, '\n');
        html = html.replace(/<\/?(thead|tbody|tfoot)[^>]*>/gi, '\n');
        html = html.replace(/<\/?[^>]+>/gi, '');

        html = html.trim().replace(/\n\s*\n/g, '\n');

        tableTexts.forEach((tableText, index) => {
            html = html.replace(`[[TABLE_${index}]]`, tableText);
        });

        html = html.replace(/\*/g, '');

        return html;
    },

    convertTableToText(tableHtml) {
        const headerRow = tableHtml.match(/<tr[^>]*>([\s\S]*?)<\/tr>/gi)?.[0] || '';
        const headers = headerRow.match(/<th[^>]*>(.*?)<\/th>/gi) || [];
        const headerText = headers.map(header => header.replace(/<\/?th[^>]*>/gi, '').trim()).join(' | ');

        const dataRows = tableHtml.match(/<tr[^>]*>([\s\S]*?)<\/tr>/gi) || [];
        const dataText = dataRows.map(row => {
            const cells = row.match(/<td[^>]*>(.*?)<\/td>/gi) || [];
            return cells.map(cell => cell.replace(/<\/?td[^>]*>/gi, '').trim()).join(' | ');
        }).filter(row => row !== '').join('\n');

        return headerText + ' |\n' + dataText + ' |';
    },

    truncateToTwoWords(text,limit = 2) {
        if (!text) return ""; 
        let words = text.trim().split(/\s+/); 
        return words.length > 2 ? words.slice(0, limit).join(" ") + "..." : text;
    }

}
