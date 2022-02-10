import dom from "@left4code/tw-starter/dist/js/dom";
import { ref } from "vue";

// Setup side menu
const findActiveMenu = (subMenu) => {
  let match = false;
  subMenu.forEach((item) => {
    if (item.namespace.split('.')[0] === route().current().split('.')[0] && !item.ignore) {
      match = true;
    } else if (!match && item.subMenu) {
      match = findActiveMenu(item.subMenu);
    }
  });
  return match;
};

const nestedMenu = (menu) => {
  menu.forEach((item, key) => {
    if (typeof item !== "string") {
      let menuItem = menu[key];
      menuItem.active =
        (item.namespace.split('.')[0] === route().current().split('.')[0] ||
          (item.subMenu && findActiveMenu(item.subMenu))) &&
        !item.ignore;

      if (item.subMenu) {
        menuItem.activeDropdown = findActiveMenu(item.subMenu);
        menuItem = {
          ...item,
          ...nestedMenu(item.subMenu),
        };
      }
    }
  });

  return menu;
};

const linkTo = (menu, router, event) => {
  if (menu.subMenu) {
    menu.activeDropdown = !menu.activeDropdown;
  } else {
    event.preventDefault();
    router.push({
      name: menu.pageName,
    });
  }
};

const enter = (el, done) => {
  dom(el).slideDown(300);
};

const leave = (el, done) => {
  dom(el).slideUp(300);
};

//index

// Toggle search dropdown
const searchDropdown = ref(false);
const showSearchDropdown = () => {
  searchDropdown.value = true;
};
const hideSearchDropdown = () => {
  searchDropdown.value = false;
};

export { nestedMenu, linkTo, enter, leave, searchDropdown, showSearchDropdown, hideSearchDropdown };
