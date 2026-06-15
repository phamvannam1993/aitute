<template>
    <div class="relative w-32 md:w-48">
        <!-- Input field for date and time -->
        <div @click="toggleCalendar" class="border border-[#B5B5BE] p-2 rounded-[10px]  cursor-pointer bg-white">
            <span class="text-[#B5B5BE] text-xs">
                {{ selectedDateTime }}
            </span>
        </div>

        <!-- Calendar (shows when showCalendar is true) -->
        <div v-if="showCalendar" ref="calendarRef"
            class="absolute bg-white shadow-lg mt-2 p-4 rounded-lg z-10 w-72 right-0">
            <!-- Calendar Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">{{ currentMonthName }} {{ currentYear }}</h2>
                <div class="flex items-center">
                    <button type="button" @click="prevMonth"
                        class="p-2 text-gray-600 bg-gray-200 rounded-full hover:bg-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12.707 15.293a1 1 0 010 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L10.414 12l3.293 3.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button type="button" @click="nextMonth"
                        class="p-2 text-gray-600 bg-gray-200 rounded-full hover:bg-gray-300 ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M7.293 4.707a1 1 0 010-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L9.586 10 5.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Days of the week -->
            <div class="grid grid-cols-7 text-center text-gray-600 mb-2">
                <div>CN</div>
                <div>T.2</div>
                <div>T.3</div>
                <div>T.4</div>
                <div>T.5</div>
                <div>T.6</div>
                <div>T.7</div>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-7 text-center">
                <div v-for="(day, index) in blankDays" :key="index" class="p-2"></div> <!-- Empty days -->
                <div v-for="(day, index) in daysInMonth" :key="index + blankDays.length" class="p-2 cursor-pointer"
                    :class="{
                        'bg-blue-500 text-white rounded-full': isSelected(day),
                        'bg-blue-100 text-blue-700 rounded-full': isToday(day),
                        'text-gray-300 cursor-not-allowed': isPast(day),
                        'hover:bg-gray-200': !isToday(day) && !isSelected(day)
                    }" @click="selectDate(day)">
                    {{ day }}
                </div>
            </div>

            <!-- Time Picker -->
            <div v-if="timePicker" class="flex justify-end items-center mt-4 gap-3">
                <div class="flex flex-col cursor-pointer">
                    <label for="hour">Giờ</label>
                    <select v-model="selectedHour" id="hour" class="border p-2 rounded-lg">
                        <option v-for="hour in 24" :key="hour" :value="hour">{{ hour }}</option>
                    </select>
                </div>
                <div class="flex flex-col cursor-pointer">
                    <label for="minute">Phút</label>
                    <select v-model="selectedMinute" id="minute" class="border p-2 rounded-lg">
                        <option v-for="minute in 60" :key="minute" :value="minute">{{ minute }}</option>
                    </select>
                </div>
            </div>

            <!-- Confirm Button -->
            <button @click="confirmDateTime" class="mt-4 w-full bg-blue-500 text-white p-2 rounded-lg">Xác nhận</button>
        </div>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
const { timePicker } = defineProps({
    timePicker: {
        type: Boolean,
        default: true,
    },
});

const showCalendar = ref(false);
const selectedDate = ref(null);
const selectedHour = ref(new Date().getHours());
const selectedMinute = ref(new Date().getMinutes());
const selectedDateTime = defineModel()
const selectedDay = ref(null);  // Lưu trữ ngày đã chọn

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const calendarRef = ref(null);

// Current date (formatted)
const currentDate = computed(() => {
    const today = new Date();
    return `${today.getFullYear()}/${today.getMonth() + 1}/${today.getDate()}`;
});

const currentHour = new Date().getHours();
const currentMinute = new Date().getMinutes();

// Month names
const months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 11'];
const currentMonthName = computed(() => months[currentMonth.value]);

// Days in the current month
const daysInMonth = computed(() => {
    const date = new Date(currentYear.value, currentMonth.value + 1, 0);
    return Array.from({ length: date.getDate() }, (_, i) => i + 1);
});

const firstDayOfMonth = computed(() => {
    const firstDate = new Date(currentYear.value, currentMonth.value, 1);
    return firstDate.getDay();
});

const blankDays = computed(() => {
    return Array.from({ length: firstDayOfMonth.value }, (_, i) => i);
});

const selectDate = (day) => {
    const today = new Date();
    const selected = new Date(currentYear.value, currentMonth.value, day);

    if (selected >= today.setHours(0, 0, 0, 0)) {
        selectedDay.value = day;
        selectedDate.value = `${currentYear.value}/${currentMonth.value + 1}/${day}`;
    } else {
        alert("Không thể chọn ngày trong quá khứ!");
    }
};

const confirmDateTime = () => {
    selectedDateTime.value = `${selectedDate.value} ${selectedHour.value}:${selectedMinute.value}`;
    showCalendar.value = false;
};

const isPast = (day) => {
    const today = new Date();
    const selected = new Date(currentYear.value, currentMonth.value, day);
    return selected < today.setHours(0, 0, 0, 0);
};

const isToday = (day) => {
    const today = new Date();
    return today.getDate() === day && today.getMonth() === currentMonth.value && today.getFullYear() === currentYear.value;
};

const isSelected = (day) => {
    return selectedDay.value === day;
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const toggleCalendar = () => {
    showCalendar.value = !showCalendar.value;
};

const handleClickOutside = (event) => {
    if (calendarRef.value && !calendarRef.value.contains(event.target) && !event.target.closest(".cursor-pointer")) {
        showCalendar.value = false;
    }
};

watch(selectedDateTime, () => {
    updateSelectedDateTime()
});

const updateSelectedDateTime = () => {
    let defaultSelectedDateTime = selectedDateTime.value ? selectedDateTime.value : `${currentDate.value} ${currentHour}:${currentMinute}`;
    let dateSelectedDateTime = new Date(defaultSelectedDateTime);
    selectDate(dateSelectedDateTime.getDate());
    // selectedHour.value = dateSelectedDateTime.getHours()
    // selectedMinute.value = dateSelectedDateTime.getMinutes()
    // confirmDateTime()
};

onMounted(() => {
    updateSelectedDateTime()
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: none;
    padding: 0.5em;
}

select::-ms-expand {
    display: none;
}
</style>
