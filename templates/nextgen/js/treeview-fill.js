var tree = [
        {
        text: "Tools",
        nodes: [
                {
                    text: "Import",
                    href: "./tools_import.php"

                },
                {
                    text: "Child 2"
                }
               ]
        },
  {
    text: "Zeige"
  },
  {
    text: "Bearbeiten"
  },
  {
    text: "System"
  },
  {
    text: "Hilfe"
  }
];

    $('#tree-managment').treeview({data: tree, enableLinks: true});
