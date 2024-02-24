# 
# 
# 
# Najskor zoberies subor budes prechadzat kazde pismeno v subore (mozno mozes nejako zobrat cele slova)tomu budes zaradzovat typy
# Potom na to pustis syntakticky analyzator spravis jednoduchu gramatiku a budes to overovat
# Nejako vyztvorit XML
#
#   cd project1
#   cat source.IPPcode24 | python3.10 parse.py
# 
#
#  MOC TO NERIES VAR / SYMB ROZDIELY DAVAJ POZOR KED TAK FIX TREBA
#
import sys
import re
import xml.etree.ElementTree as ET # alias
import xml.dom.minidom

def arg_help():
    print("This is a simple help message.")
    print("Usage: python script.py [options]")
    print("-h, --help: Display this help message")
    sys.exit(0)

# symbol moze byt var alebo literal
# literal moze byt bool, int, string, nil
    
# var moze byt len GF@, LF@, TF@
class Symb:
    pass

class Type(Symb): ## zmen pozor type je cisto bool or string or int  pohraj sa s tym 
    pass

class Var(Symb): # symb je subclasa var 
    pass

class Label:
    pass

class VSL(Label, Var, Type, Symb):
    pass

# class EOL:
#     pass


class Token:
    def __init__(self, data, type):
        self.data = data
        self.type = type
    def printAtr(self):
        return f"Token: {'/n'} Type: {self.type}" if self.data == "\n" else f"Token: {self.data} Type: {self.type}"

def prettify_xml(xml_string):
    ugly_xml = xml.dom.minidom.parseString(xml_string)
    return ugly_xml.toprettyxml(encoding="UTF-8")

def VarSymbLabel(type):
    if type == "var":
        return Var
    elif type == "string" or type == "bool" or type == "int":
        return Type
    elif type == "nil":
        return Symb
    elif type == "label":
        return Label
    # elif type == "EOL":
    #     return EOL
def printVarSymbLabel(type):
    if type == Symb:
        return "Symb"
    elif type == Var:
        return "Var"
    elif type == Label:
        return "Label"
    elif type == Type:
        return "Type"
    # elif type == EOL:
    #     return "EOL"

INSTRUCTIONS: dict[str, list[VSL]] = {
    "MOVE": [Var, Symb],
    "CREATEFRAME": [],
    "PUSHFRAME": [],
    "POPFRAME": [],
    "DEFVAR": [Var],
    "CALL": [Label],
    "RETURN": [],
    "PUSHS": [Symb],
    "POPS": [Var],
    "ADD": [Var, Symb, Symb],
    "SUB": [Var, Symb, Symb],
    "MUL": [Var, Symb, Symb],
    "IDIV": [Var, Symb, Symb],
    "LT": [Var, Symb, Symb],
    "GT": [Var, Symb, Symb],
    "EQ": [Var, Symb, Symb],
    "AND": [Var, Symb, Symb],
    "OR": [Var, Symb, Symb],
    "NOT": [Var, Symb],
    "INT2CHAR": [Var, Symb],
    "STRI2INT": [Var, Symb, Symb],
    "READ": [Var, Type],
    "WRITE": [Symb],
    "CONCAT": [Var, Symb, Symb],
    "STRLEN": [Var, Symb],
    "GETCHAR": [Var, Symb, Symb],
    "SETCHAR": [Var, Symb, Symb],
    "TYPE": [Var, Symb],
    "LABEL": [Label],
    "JUMP": [Label],
    "JUMPIFEQ": [Label, Symb, Symb],
    "JUMPIFNEQ": [Label, Symb, Symb],
    "EXIT": [Symb],
    "DPRINT": [Symb],
    "BREAK": [],
}

def main():
    array = []


    if "--help" in sys.argv:
        arg_help()
  
    file = sys.stdin.read()

    # Remove comments from input text
    cleaned_text = re.sub(r"#.*?$", '', file, flags=re.MULTILINE)

    # Extract tokens from cleaned text
    tokens = re.findall(r"\S+", cleaned_text)


    for token_data in tokens:
        token = Token(token_data, "")  # Create a Token instance for each token
        set_type(token)  # Set the type for the token
        array.append(token)

    # for token in array:
    #     print(token.printAtr())
    # # print(array[0].printAtr())
    output = parser(array)
    # print("return shit : ", output)
    sys.exit(output)
        
    # if parser(array):
    #     print("Valid")  
    # else:
    #     print("Invalid") 

    sys.exit(0)


def set_type(token):

    if re.match(r"(?:bool@(true|false))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "bool"
    elif re.match(r"(?:string@((?:\\[0-9]{3}|[^\n\t #]|)+))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "string"
    elif re.match(r"(?:(nil@(nil)))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "nil"
    elif re.match(r"(?:int@([+-]?[0-9]+))", token.data):
        token.data = token.data.split("@")[1] #uchova substring po @
        token.type = "int"
    elif re.match(r"(?:GF@([a-zA-Z0-9_\-$&%*!?])+)", token.data):
        token.type = "var"
    elif re.match(r"(?:LF@([a-zA-Z0-9_\-$&%*!?])+)", token.data):
        token.type = "var"
    elif re.match(r"(?:TF@([a-zA-Z0-9_\-$&%*!?])+)", token.data):
        token.type = "var"
    elif token.data == "MOVE":  
        token.type = "MOVE"
    elif token.data == "CREATEFRAME":  
        token.type = "CREATEFRAME"
    elif token.data == "PUSHFRAME":
        token.type = "PUSHFRAME"
    elif token.data == "POPFRAME":
        token.type = "POPFRAME"
    elif token.data == "DEFVAR":
        token.type = "DEFVAR"
    elif token.data == "CALL":
        token.type = "CALL"
    elif token.data == "RETURN":
        token.type = "RETURN"
    elif token.data == "PUSHS":
        token.type = "PUSHS"
    elif token.data == "POPS":
        token.type = "POPS"
    elif token.data == "ADD":
        token.type = "ADD"
    elif token.data == "SUB":
        token.type = "SUB"
    elif token.data == "MUL":
        token.type = "MUL"
    elif token.data == "IDIV":
        token.type = "IDIV"
    elif token.data == "LT":
        token.type = "LT"
    elif token.data == "GT":
        token.type = "GT"
    elif token.data == "EQ":
        token.type = "EQ"
    elif token.data == "AND":
        token.type = "AND"
    elif token.data == "OR":
        token.type = "OR"
    elif token.data == "NOT":
        token.type = "NOT"
    elif token.data == "INT2CHAR":
        token.type = "INT2CHAR"
    elif token.data == "STRI2INT":
        token.type = "STRI2INT"
    elif token.data == "READ":
        token.type = "READ"
    elif token.data == "WRITE":
        token.type = "WRITE"
    elif token.data == "CONCAT":
        token.type = "CONCAT"
    elif token.data == "STRLEN":
        token.type = "STRLEN"
    elif token.data == "GETCHAR":
        token.type = "GETCHAR"
    elif token.data == "SETCHAR":
        token.type = "SETCHAR"
    elif token.data == "TYPE":
        token.type = "TYPE"
    elif token.data == "LABEL":
        token.type = "LABEL"
    elif token.data == "JUMP":
        token.type = "JUMP"
    elif token.data == "JUMPIFEQ":
        token.type = "JUMPIFEQ"
    elif token.data == "JUMPIFNEQ":
        token.type = "JUMPIFNEQ"
    elif token.data == "EXIT":
        token.type = "EXIT"
    elif token.data == "DPRINT":
        token.type = "DPRINT"
    elif token.data == "BREAK":
        token.type = "BREAK"
    elif token.data == ".IPPcode24":
        token.type = "header"
    elif re.match(r"(?:[A-Za-z])", token.data):
        token.type = "label"
    # elif token.data == "\n":
    #     token.type = "EOL"
    else:
        token.type = "BAD_TOKEN"

    return True

def parser(token_array):
    # print("\n------------PARSER------------\n")
    root = ET.Element("program")
    root.set("language", "IPPcode24")


    i = 0
    j = 0
    order = 1
    if token_array[0].type != "header" :
        return 21
    
    token_array.pop(0)
    # token_array.pop(0) # remove the EOL token

    while i < len(token_array):
        if token_array[i].type not in INSTRUCTIONS:
            print("array position ")
            print(i)
            # print("prev instructions " + token_array[i-1].type)
            print("not in instructions " + token_array[i].type)
            return 23
        excepted_tokens = INSTRUCTIONS[token_array[i].type]
        # vytvoris elemment pre danu instrukciu
        instruction = ET.SubElement(root, "instruction")
        instruction.set("order", str(order))
        instruction.set("opcode", token_array[i].type)
        i += 1 # proc to tu je dpc ????
        while j < len(excepted_tokens):
            if i >= len(token_array): # malo argumentov
                return 23
            arg = ET.SubElement(instruction, "arg" + str(j+1))
            arg.set("type", token_array[i].type)
            arg.text = token_array[i].data
            # budes udavat argumenty danej instrukcii
            # if type(VarSymbLabel(token_array[i].type)) !=  type(excepted_tokens[j]):
            if (VarSymbLabel(token_array[i].type) != excepted_tokens[j]) and not(issubclass(VarSymbLabel(token_array[i].type), excepted_tokens[j])):
                print("expected len " + str(len(excepted_tokens)))
                print("array position ")
                print(i)
                print(j)
                # print("prev instructions " + token_array[i-2].type)
                # print("prev instructions " + token_array[i-1].type)
                print("token in array : " + token_array[i].type + " : " + printVarSymbLabel(VarSymbLabel(token_array[i].type)))
                print("expected token : " + printVarSymbLabel(excepted_tokens[j]))
                print("Type returned by VarSymbLabel:", VarSymbLabel(token_array[i].type))
                print("Expected token type:", excepted_tokens[j])
                print("Type returned by issubclass:", issubclass(VarSymbLabel(token_array[i].type), excepted_tokens[j]))
                return 23
            # print("token in array : " + token_array[i].type + " : " + printVarSymbLabel(VarSymbLabel(token_array[i].type)))
            # print("expected token : " + printVarSymbLabel(excepted_tokens[j]))
            i += 1
            j += 1
        j = 0
        order += 1
    xml_str = ET.tostring(root, encoding='utf-8')
    prettified_xml = prettify_xml(xml_str)
    with open("output.xml", "wb") as output_file:  # Use binary mode for writing
        output_file.write(prettified_xml)
    good = prettified_xml.decode("utf-8")
    print(good, file=sys.stdout)
    return 0
    

main()
