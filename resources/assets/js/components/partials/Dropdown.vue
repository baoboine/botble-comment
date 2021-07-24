<template>
    <div class="bb-dropdown" :class="{'no-select': noSelectMode, 'no-caret': !(options && options.length)}">
        <li @click="toggleMenu()" class="bb-dropdown-toggle" v-if="selectedOption.name !== undefined">
            <i :class="icon" v-if="icon" />
            {{ selectedOption.name }}
            <span class="caret" v-if="options && options.length"></span>
        </li>

        <li @click="toggleMenu()" class="bb-dropdown-toggle bb-dropdown-toggle-placeholder" v-if="selectedOption.name === undefined">
            {{placeholderText}}
            <span class="caret" v-if="options && options.length"></span>
        </li>

        <ul class="bb-dropdown-menu" v-if="showMenu && options && options.length">
            <li v-for="(option, idx) in options" :key="idx">
                <a href="javascript:void(0)" @click="option.onClick ? option.onClick() : updateOption(option)">
                    {{ option.name }}
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedOption: {
                name: '',
            },
            showMenu: false,
            placeholderText: this.__('Please select an item'),
        }
    },
    props: {
        options: {
            type: [Array, Object]
        },
        selected: {},
        selectedValue: {},
        placeholder: [String],
        closeOnOutsideClick: {
            type: [Boolean],
            default: true,
        },
        noSelectMode: {
            type: Boolean,
            default: false,
        },
        icon: {
            type: String,
            default: null,
        }
    },
    mounted() {
        if (this.selectedValue) {
            this.selectedOption = this.options.find(item => item.value === this.selectedValue);
        } else {
            this.selectedOption = this.selected;
        }
        if (this.placeholder)
        {
            this.placeholderText = this.placeholder;
        }
        if (this.closeOnOutsideClick) {
            document.addEventListener('click', this.clickHandler);
        }
    },
    watch: {
        selected() {
            if (this.selectedValue) {
                this.selectedOption = this.options.find(item => item.value === this.selectedValue);
            } else {
                this.selectedOption = this.selected;
            }
        }
    },
    beforeDestroy() {
        document.removeEventListener('click', this.clickHandler);
    },
    methods: {
        updateOption(option) {
            if (!this.noSelectMode) {
                this.selectedOption = option;
                this.showMenu = false;
                this.$emit('updateOption', this.selectedOption);
            }
        },
        toggleMenu() {
            this.showMenu = !this.showMenu;
            this.$emit('click');
        },
        clickHandler(event) {
            const { target } = event;
            const { $el } = this;
            if (!$el.contains(target)) {
                this.showMenu = false;
            }
        },
    }
}
</script>
