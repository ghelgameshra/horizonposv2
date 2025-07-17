import './bootstrap';

import Alpine from 'alpinejs';
import { createTableOrder, generateInputGroup } from './utils/kasirHepler';
import { formatRupiah, terbilangRupiah } from './utils/numberFormat';
import { showSelect2 } from './utils/select2';
import { reloadDataTable } from './utils/datatable';
import { formatTanggal, notification } from './utils/view';

window.Alpine = Alpine;
window.createTableOrder = createTableOrder;
window.generateInputGroup = generateInputGroup;
window.terbilangRupiah = terbilangRupiah;
window.formatRupiah = formatRupiah;
window.showSelect2 = showSelect2;
window.reloadDataTable = reloadDataTable;
window.notification = notification;
window.formatTanggal = formatTanggal;

Alpine.start();
