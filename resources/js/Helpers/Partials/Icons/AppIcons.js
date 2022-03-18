import * as LucideIcons from "lucide-vue-next";
import { defineComponent, h } from "vue";

const defaultClass = {
    class: "lucide",
};

function createIcon(_icon) {
    return defineComponent({
        render() {
            return h(LucideIcons[_icon.slice(0, _icon.search("Icon"))], { class: "lucide" });
        },
    });
}

const ChevronDownIcon = createIcon("ChevronDownIcon");
const ChevronUpIcon = createIcon("ChevronUpIcon");
const ChevronLeftIcon = createIcon("ChevronLeftIcon");
const ChevronsLeftIcon = createIcon("ChevronsLeftIcon");
const BarChart2Icon = createIcon("BarChart2Icon");
const PlusIcon = createIcon("PlusIcon");
const TrashIcon = createIcon("TrashIcon");
const PlusCircleIcon = createIcon("PlusCircleIcon");
const PencilIcon = createIcon("PencilIcon");
const EditIcon = createIcon("EditIcon");
const EyeIcon = createIcon("EyeIcon");
const PaperclipIcon = createIcon("PaperclipIcon");
const ToggleRightIcon = createIcon("ToggleRightIcon");
const FolderIcon = createIcon("FolderIcon");
const ShuffleIcon = createIcon("ShuffleIcon");
const MaximizeIcon = createIcon("MaximizeIcon");
const MinimizeIcon = createIcon("MinimizeIcon");
const PlusSquareIcon = createIcon("PlusSquareIcon");
const MinusSquareIcon = createIcon("MinusSquareIcon");
const FolderPlusIcon = createIcon("FolderPlusIcon");
const FileIcon = createIcon("FileIcon");
const UserIcon = createIcon("UserIcon");
const UsersIcon = createIcon("UsersIcon");
const SearchIcon = createIcon("SearchIcon");
const SettingsIcon = createIcon("SettingsIcon");
const MoreVerticalIcon = createIcon("MoreVerticalIcon");
const MoreHorizontalIcon = createIcon("MoreHorizontalIcon");
const DownloadCloudIcon = createIcon("DownloadCloudIcon");
const Edit3Icon = createIcon("Edit3Icon");
const HistoryIcon = createIcon("HistoryIcon");
const MessageSquareIcon = createIcon("MessageSquareIcon");
const TargetIcon = createIcon("TargetIcon");
const CalendarIcon = createIcon("CalendarIcon");
const ActivityIcon = createIcon("ActivityIcon");
const ZapIcon = createIcon("ZapIcon");
const MinusIcon = createIcon("MinusIcon");
const SendIcon = createIcon("SendIcon");
const ImagePlusIcon = createIcon("ImagePlusIcon");

const AuthIcon = {
    ToggleRightIcon,
    FolderIcon,
    ChevronDownIcon,
    ShuffleIcon,
    MaximizeIcon,
    MinimizeIcon,
    PlusSquareIcon,
    MinusSquareIcon,
    HistoryIcon,
    MessageSquareIcon,
};

const FinderIcon = {
    ChevronLeftIcon,
    FolderPlusIcon,
    FolderIcon,
    FileIcon,
    UserIcon,
    UsersIcon,
    TrashIcon,
    PlusIcon,
    SearchIcon,
    ChevronDownIcon,
    SettingsIcon,
    ChevronsLeftIcon,
};
//import { AuthIcon } from "@/Helpers/Partials/Icons/AppIcons.js";
export {
    ActivityIcon,
    MinusIcon,
    ImagePlusIcon,
    SendIcon,
    ZapIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    ChevronLeftIcon,
    BarChart2Icon,
    PlusIcon,
    TrashIcon,
    PlusCircleIcon,
    PencilIcon,
    EditIcon,
    EyeIcon,
    PaperclipIcon,
    ToggleRightIcon,
    FolderIcon,
    ShuffleIcon,
    MaximizeIcon,
    MinimizeIcon,
    PlusSquareIcon,
    MinusSquareIcon,
    FolderPlusIcon,
    FileIcon,
    UserIcon,
    UsersIcon,
    SearchIcon,
    SettingsIcon,
    MoreVerticalIcon,
    DownloadCloudIcon,
    Edit3Icon,
    FinderIcon,
    TargetIcon,
    CalendarIcon,
    AuthIcon,
    MoreHorizontalIcon,
};
